<?php

namespace App\Traits;

use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

trait HasImages
{
    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        $imageService = app(ImageService::class);
        $pathField = $this->getImagePathField();
        $fallback = $this->getImageFallback();

        return $imageService->getUrl($this->{$pathField}, $fallback);
    }

    /**
     * Upload and save an image
     */
    public function uploadImage($file, array $options = []): array
    {
        $imageService = app(ImageService::class);
        $directory = $this->getImageDirectory();
        $uploadOptions = array_merge($imageService->getUploadOptions($directory), $options);

        $result = $imageService->upload($file, $directory, $uploadOptions);

        if ($result['success']) {
            $pathField = $this->getImagePathField();
            $this->{$pathField} = $result['path'];
            $this->save();
        }

        return $result;
    }

    /**
     * Delete the current image
     */
    public function deleteImage(): bool
    {
        $imageService = app(ImageService::class);
        $pathField = $this->getImagePathField();

        $deleted = $imageService->delete($this->{$pathField});

        if ($deleted) {
            $this->{$pathField} = null;
            $this->save();
        }

        return $deleted;
    }

    /**
     * Check if the model has an image
     */
    public function hasImage(): bool
    {
        $imageService = app(ImageService::class);
        $pathField = $this->getImagePathField();

        return $imageService->exists($this->{$pathField});
    }

    /**
     * Get the image path field name
     * Override this in your model if needed
     */
    protected function getImagePathField(): string
    {
        return 'image_path';
    }

    /**
     * Get the image directory for uploads
     * Override this in your model if needed
     */
    protected function getImageDirectory(): string
    {
        return 'images';
    }

    /**
     * Get the fallback image path
     * Override this in your model if needed
     */
    protected function getImageFallback(): ?string
    {
        return null;
    }

    /**
     * Boot the trait and add model events
     */
    protected static function bootHasImages()
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });
    }
}
