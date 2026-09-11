<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Traits\HasImages;
use App\Traits\Translatable;
use App\Models\Product;

class Category extends Model
{
    use AsSource, Filterable, HasImages, Translatable;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'is_active', 'sort_order',
        'name_en', 'name_ar', 'slug_en', 'slug_ar', 'description_en', 'description_ar',
    ];

    // For Orchid tables
    protected $allowedSorts   = ['name', 'is_active', 'sort_order', 'created_at'];
    protected $allowedFilters = ['name', 'is_active'];

    // Computed attributes
    protected $appends = ['image_url'];

    // Relationships
    public function parent()   { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id'); }
    public function products() { return $this->hasMany(Product::class); }

    /* ==== Required scopes for home page ==== */

    // Return only active categories
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // Return only top-level categories (without parent)
    public function scopeTopLevel($q)
    {
        return $q->whereNull('parent_id');
    }

    /**
     * Override trait methods for category-specific configuration
     */
    protected function getImageDirectory(): string
    {
        return 'categories';
    }

    protected function getImageFallback(): ?string
    {
        return 'images/placeholder-category.png';
    }

    /**
     * Override the image URL generation to ensure proper host
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return asset($this->getImageFallback());
        }

        $imageService = app(\App\Services\ImageService::class);
        $url = $imageService->getUrl($this->image_path, $this->getImageFallback());

        // Always use 127.0.0.1:8000 for local development
        if (str_contains($url, 'localhost')) {
            $url = str_replace('localhost', '127.0.0.1:8000', $url);
        }

        return $url;
    }

    /**
     * Get localized name attribute
     */
    public function getNameAttribute($value)
    {
        return $this->getLocalizedAttribute('name') ?? $value;
    }

    /**
     * Get localized description attribute
     */
    public function getDescriptionAttribute($value)
    {
        return $this->getLocalizedAttribute('description') ?? $value;
    }

    /**
     * Get localized slug attribute
     */
    public function getSlugAttribute($value)
    {
        return $this->getLocalizedAttribute('slug') ?? $value;
    }
}
