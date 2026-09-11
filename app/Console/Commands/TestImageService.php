<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestImageService extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'images:test {--check-storage : Check storage configuration}';

    /**
     * The console command description.
     */
    protected $description = 'Test ImageService functionality and storage configuration';

    /**
     * Execute the console command.
     */
    public function handle(ImageService $imageService): int
    {
        $this->info('Testing ImageService...');

        // Test storage configuration
        if ($this->option('check-storage') || true) {
            $this->info('Checking storage configuration...');

            if ($imageService->isStorageConfigured()) {
                $this->info('✅ Storage is properly configured');
            } else {
                $this->error('❌ Storage configuration failed');
                return 1;
            }
        }

        // Test existing slide images
        $this->info('Testing existing slide images...');

        $slidesPath = 'slides';
        if (Storage::disk('public')->exists($slidesPath)) {
            $files = Storage::disk('public')->files($slidesPath);
            $this->info("Found {count($files)} files in slides directory");

            foreach ($files as $file) {
                $url = $imageService->getUrl($file);
                if ($url) {
                    $this->info("✅ {$file} -> {$url}");
                } else {
                    $this->error("❌ Failed to generate URL for {$file}");
                }
            }
        } else {
            $this->warn('No slides directory found');
        }

        // Test fallback URLs
        $this->info('Testing fallback URLs...');
        $fallbackUrl = $imageService->getFallbackUrl('slides');
        $this->info("Fallback URL for slides: {$fallbackUrl}");

        // Test optimized URL generation
        $this->info('Testing optimized URL generation...');
        $testPath = 'slides/NkSCnSbJvNROGlOw.jpg'; // Your actual slide image
        $optimizedUrl = $imageService->getOptimizedUrl($testPath, 'slides');
        $this->info("Optimized URL: {$optimizedUrl}");

        $this->info('ImageService test completed successfully!');
        return 0;
    }
}
