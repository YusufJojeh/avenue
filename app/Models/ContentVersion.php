<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContentVersion extends Model
{
    protected $fillable = [
        'content_type',
        'content_id',
        'locale',
        'version_number',
        'content_data',
        'is_published'
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_published' => 'boolean',
        'version_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the content that owns this version
     */
    public function content()
    {
        return $this->morphTo('content');
    }

    /**
     * Scope to get only published versions
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get latest versions
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('version_number', 'desc');
    }

    /**
     * Scope to get versions for specific content
     */
    public function scopeForContent(Builder $query, string $type, int $id): Builder
    {
        return $query->where('content_type', $type)->where('content_id', $id);
    }

    /**
     * Scope to get versions for specific locale
     */
    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    /**
     * Get the latest published version for specific content and locale
     */
    public static function getLatestPublished(string $contentType, int $contentId, string $locale)
    {
        return static::forContent($contentType, $contentId)
            ->forLocale($locale)
            ->published()
            ->latest()
            ->first();
    }

    /**
     * Create a new version
     */
    public static function createVersion(string $contentType, int $contentId, string $locale, array $contentData, bool $publish = false): self
    {
        // Get the next version number
        $lastVersion = static::forContent($contentType, $contentId)
            ->forLocale($locale)
            ->latest()
            ->first();

        $versionNumber = $lastVersion ? $lastVersion->version_number + 1 : 1;

        // If publishing, unpublish all other versions
        if ($publish) {
            static::forContent($contentType, $contentId)
                ->forLocale($locale)
                ->update(['is_published' => false]);
        }

        return static::create([
            'content_type' => $contentType,
            'content_id' => $contentId,
            'locale' => $locale,
            'version_number' => $versionNumber,
            'content_data' => $contentData,
            'is_published' => $publish,
        ]);
    }

    /**
     * Publish this version
     */
    public function publish(): bool
    {
        // Unpublish all other versions of the same content and locale
        static::forContent($this->content_type, $this->content_id)
            ->forLocale($this->locale)
            ->where('id', '!=', $this->id)
            ->update(['is_published' => false]);

        // Publish this version
        return $this->update(['is_published' => true]);
    }

    /**
     * Unpublish this version
     */
    public function unpublish(): bool
    {
        return $this->update(['is_published' => false]);
    }

    /**
     * Get content data for specific field
     */
    public function getField(string $field, $default = null)
    {
        return $this->content_data[$field] ?? $default;
    }

    /**
     * Set content data for specific field
     */
    public function setField(string $field, $value): void
    {
        $this->content_data[$field] = $value;
        $this->save();
    }

    /**
     * Get content summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'content_type' => $this->content_type,
            'content_id' => $this->content_id,
            'locale' => $this->locale,
            'version_number' => $this->version_number,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'content_data' => $this->content_data,
        ];
    }

    /**
     * Check if this is the latest version
     */
    public function isLatest(): bool
    {
        $latestVersion = static::forContent($this->content_type, $this->content_id)
            ->forLocale($this->locale)
            ->latest()
            ->first();

        return $latestVersion && $latestVersion->id === $this->id;
    }

    /**
     * Get version history for content
     */
    public static function getVersionHistory(string $contentType, int $contentId, string $locale): \Illuminate\Database\Eloquent\Collection
    {
        return static::forContent($contentType, $contentId)
            ->forLocale($locale)
            ->orderBy('version_number', 'desc')
            ->get();
    }
}
