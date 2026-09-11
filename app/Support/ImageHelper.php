<?php

namespace App\Support;

use App\Services\ImageService;

class ImageHelper
{
    /**
     * Get the URL for an image path
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        $imageService = app(ImageService::class);
        return $imageService->getUrl($path, $fallback);
    }

    /**
     * Check if an image exists
     */
    public static function exists(?string $path): bool
    {
        $imageService = app(ImageService::class);
        return $imageService->exists($path);
    }

    /**
     * Delete an image
     */
    public static function delete(?string $path): bool
    {
        $imageService = app(ImageService::class);
        return $imageService->delete($path);
    }

    /**
     * Get placeholder image URL
     */
    public static function placeholder(string $type = 'product'): string
    {
        $placeholders = [
            'product' => 'images/placeholder-product.png',
            'category' => 'images/placeholder-category.png',
            'brand' => 'images/placeholder-brand.png',
        ];

        return asset($placeholders[$type] ?? $placeholders['product']);
    }
}
