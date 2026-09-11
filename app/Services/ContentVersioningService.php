<?php

namespace App\Services;

use App\Models\ContentVersion;
use Illuminate\Support\Facades\DB;

class ContentVersioningService
{
    /**
     * Create a new version of content
     */
    public function createVersion(string $contentType, int $contentId, string $locale, array $contentData, bool $publish = false): ContentVersion
    {
        return ContentVersion::createVersion($contentType, $contentId, $locale, $contentData, $publish);
    }

    /**
     * Get the latest published version
     */
    public function getLatestPublished(string $contentType, int $contentId, string $locale): ?ContentVersion
    {
        return ContentVersion::getLatestPublished($contentType, $contentId, $locale);
    }

    /**
     * Get version history
     */
    public function getVersionHistory(string $contentType, int $contentId, string $locale): \Illuminate\Database\Eloquent\Collection
    {
        return ContentVersion::getVersionHistory($contentType, $contentId, $locale);
    }

    /**
     * Publish a specific version
     */
    public function publishVersion(int $versionId): bool
    {
        $version = ContentVersion::findOrFail($versionId);
        return $version->publish();
    }

    /**
     * Get version statistics
     */
    public function getVersionStats(): array
    {
        $totalVersions = ContentVersion::count();
        $publishedVersions = ContentVersion::published()->count();

        return [
            'total_versions' => $totalVersions,
            'published_versions' => $publishedVersions,
            'unpublished_versions' => $totalVersions - $publishedVersions,
        ];
    }
}
