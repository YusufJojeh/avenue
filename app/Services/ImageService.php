<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Default disk for image storage
     */
    protected string $disk = 'public';

    /**
     * Check if we're in production and need to use direct file access
     */
    protected function isProductionWithDirectAccess(): bool
    {
        return app()->environment('production') &&
               !is_link(public_path('storage')) &&
               !is_dir(public_path('storage'));
    }

    /**
     * Maximum file size in bytes (5MB)
     */
    protected int $maxFileSize = 5242880;

    /**
     * Allowed image mime types
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif'
    ];

    /**
     * Upload and process an image
     */
    public function upload(UploadedFile $file, string $directory, array $options = []): array
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $this->generateFilename($file);

            // Store original file
            $path = $file->storeAs($directory, $filename, $this->disk);

            // Set public visibility
            Storage::disk($this->disk)->setVisibility($path, 'public');

            // Process image if optimization is enabled and Intervention Image is available
            $processedPath = $this->processImage($path, $options);

            return [
                'success' => true,
                'path' => $processedPath ?: $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'url' => $this->getUrl($processedPath ?: $path)
            ];

        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'directory' => $directory,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete an image from storage
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return true;
        }

        try {
            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->delete($path);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

        /**
     * Get the public URL for an image
     */
    public function getUrl(?string $path, ?string $fallback = null): ?string
    {
        if (!$path) {
            return $fallback ? asset($fallback) : null;
        }

        try {
            // Check if image exists before generating URL
            if (!$this->exists($path)) {
                Log::warning('Image not found: ' . $path);
                return $fallback ? asset($fallback) : null;
            }

            $url = Storage::disk($this->disk)->url($path);

            // Fix localhost URL for development if needed
            if (app()->environment('local') && str_contains($url, 'localhost')) {
                $url = str_replace('localhost', '127.0.0.1:8000', $url);
            }

            // Ensure production URLs use the correct domain
            if (app()->environment('production')) {
                if (str_contains($url, '127.0.0.1')) {
                    $url = str_replace('127.0.0.1:8000', 'avenuebrand.online', $url);
                }
                // Ensure HTTPS in production
                if (!str_starts_with($url, 'https://')) {
                    $url = str_replace('http://', 'https://', $url);
                }
            }

            // Handle production without storage link
            if ($this->isProductionWithDirectAccess()) {
                $url = asset('storage/' . $path);
            }

            // Ensure HTTPS in production
            if (app()->environment('production') && !str_starts_with($url, 'https://')) {
                $url = str_replace('http://', 'https://', $url);
            }

            return $url;
        } catch (\Exception $e) {
            Log::error('Image URL generation failed: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return $fallback ? asset($fallback) : null;
        }
    }

    /**
     * Check if an image exists
     */
    public function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            $exists = Storage::disk($this->disk)->exists($path);

            // Log missing files for debugging
            if (!$exists) {
                Log::warning('Image file not found: ' . $path, [
                    'path' => $path,
                    'disk' => $this->disk,
                    'storage_path' => Storage::disk($this->disk)->path($path)
                ]);
            }

            return $exists;
        } catch (\Exception $e) {
            Log::error('Error checking if image exists: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get fallback image URL based on directory type
     */
    public function getFallbackUrl(string $directory = 'default'): string
    {
        $fallbacks = [
            'slides' => 'images/placeholder-slide.png',
            'products' => 'images/placeholder-product.png',
            'categories' => 'images/placeholder-category.png',
            'brands' => 'images/placeholder-brand.png',
            'offers' => 'images/placeholder-offer.png',
            'default' => 'images/placeholder-image.png'
        ];

        $fallback = $fallbacks[$directory] ?? $fallbacks['default'];
        return asset($fallback);
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('File size exceeds maximum limit of ' . ($this->maxFileSize / 1024 / 1024) . 'MB');
        }

        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $this->allowedMimeTypes));
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);

        return "{$name}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Process image (resize, optimize, etc.)
     * This method is optional and will gracefully handle missing Intervention Image
     */
    protected function processImage(string $path, array $options = []): ?string
    {
        // Check if Intervention Image is available
        if (!class_exists(Image::class)) {
            Log::info('Intervention Image not available, skipping image processing');
            return null;
        }

        try {
            $fullPath = Storage::disk($this->disk)->path($path);

            // Use Intervention Image facade
            $image = Image::read($fullPath);

            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Apply options
            if (isset($options['max_width']) && $originalWidth > $options['max_width']) {
                $image->scaleDown(width: $options['max_width']);
            }

            if (isset($options['max_height']) && $originalHeight > $options['max_height']) {
                $image->scaleDown(height: $options['max_height']);
            }

            // Optimize for web
            $image->orient(); // Auto-rotate based on EXIF data

            // Save optimized image with better compression
            $quality = $options['quality'] ?? 85;
            $image->save($fullPath, quality: $quality);

            // Log optimization results
            Log::info('Image optimized', [
                'path' => $path,
                'original_size' => $originalWidth . 'x' . $originalHeight,
                'new_size' => $image->width() . 'x' . $image->height(),
                'quality' => $quality
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::warning('Image processing failed, using original: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get validation rules for image uploads
     */
    public function getValidationRules(string $fieldName = 'image', bool $required = false): array
    {
        $rules = [
            $fieldName => [
                $required ? 'required' : 'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp,gif',
                'max:' . ($this->maxFileSize / 1024) // Convert to KB
            ]
        ];

        return $rules;
    }

    /**
     * Get directory-specific upload options
     */
    public function getUploadOptions(string $directory): array
    {
        $options = [
            'products' => [
                'max_width' => 1200,
                'max_height' => 1200,
                'quality' => 85
            ],
            'categories' => [
                'max_width' => 800,
                'max_height' => 600,
                'quality' => 80
            ],
            'brands' => [
                'max_width' => 400,
                'max_height' => 200,
                'quality' => 85
            ],
            'slides' => [
                'max_width' => 1920,
                'max_height' => 800,
                'quality' => 85
            ],
            'offers' => [
                'max_width' => 800,
                'max_height' => 400,
                'quality' => 80
            ],
            'branding' => [
                'max_width' => 500,
                'max_height' => 200,
                'quality' => 90
            ]
        ];

        return $options[$directory] ?? [];
    }

    /**
     * Generate responsive image URLs for different screen sizes
     */
    public function getResponsiveUrls(string $path, array $sizes = []): array
    {
        if (!$path || !$this->exists($path)) {
            return [];
        }

        $defaultSizes = [
            'small' => ['width' => 400, 'height' => 300],
            'medium' => ['width' => 800, 'height' => 600],
            'large' => ['width' => 1200, 'height' => 900],
        ];

        $sizes = array_merge($defaultSizes, $sizes);
        $urls = [];

        foreach ($sizes as $size => $dimensions) {
            $urls[$size] = $this->getUrl($path);
        }

        return $urls;
    }

    /**
     * Get optimized image URL with fallback
     */
    public function getOptimizedUrl(?string $path, string $directory = 'default', ?string $fallback = null): ?string
    {
        if (!$path) {
            return $fallback ? asset($fallback) : $this->getFallbackUrl($directory);
        }

        // Try to get the URL
        $url = $this->getUrl($path, $fallback);

        // If URL generation failed, return fallback
        if (!$url) {
            return $fallback ? asset($fallback) : $this->getFallbackUrl($directory);
        }

        return $url;
    }

    /**
     * Check if storage is properly configured for production
     */
    public function isStorageConfigured(): bool
    {
        try {
            $testPath = 'test_' . time() . '.txt';
            $testContent = 'Storage test';

            // Test write
            Storage::disk($this->disk)->put($testPath, $testContent);

            // Test read
            $content = Storage::disk($this->disk)->get($testPath);

            // Test URL generation
            $url = Storage::disk($this->disk)->url($testPath);

            // Clean up
            Storage::disk($this->disk)->delete($testPath);

            return $content === $testContent && !empty($url);
        } catch (\Exception $e) {
            Log::error('Storage configuration test failed: ' . $e->getMessage());
            return false;
        }
    }
}
