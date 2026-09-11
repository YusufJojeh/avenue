<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Services\ImageService;

class ProductImage extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // For Orchid tables
    protected $allowedSorts   = ['sort_order', 'is_primary', 'created_at'];
    protected $allowedFilters = ['alt', 'is_primary'];

    // Computed attributes
    protected $appends = ['url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the URL for the image
     */
    public function getUrlAttribute(): ?string
    {
        if (!$this->path) {
            return null;
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($this->path);
    }

    /**
     * Upload and save a new image
     */
    public function uploadImage($file, array $options = []): array
    {
        $imageService = app(ImageService::class);
        $uploadOptions = array_merge($imageService->getUploadOptions('products'), $options);

        $result = $imageService->upload($file, 'products', $uploadOptions);

        if ($result['success']) {
            $this->path = $result['path'];
            $this->save();
        }

        return $result;
    }

    /**
     * Delete the image file
     */
    public function deleteImageFile(): bool
    {
        $imageService = app(ImageService::class);
        return $imageService->delete($this->path);
    }

    /**
     * Check if image file exists
     */
    public function imageExists(): bool
    {
        $imageService = app(ImageService::class);
        return $imageService->exists($this->path);
    }

    /**
     * Boot method to handle image deletion on model delete
     */
    protected static function booted()
    {
        static::deleting(function (ProductImage $productImage) {
            $productImage->deleteImageFile();
        });
    }
}
