<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DeployClear extends Command
{
    protected $signature = 'deploy:clear {--force : Force clear without confirmation}';
    protected $description = 'Clear all caches and prepare for deployment';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will clear all caches and temporary files. Continue?')) {
                $this->info('Deployment clear cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('🚀 Starting deployment clear process...');
        
        $this->clearApplicationCaches();
        $this->clearViewCaches();
        $this->clearRouteCaches();
        $this->clearConfigCaches();
        $this->clearEventCaches();
        $this->clearOptimizedFiles();
        $this->clearTemporaryFiles();
        $this->clearLogFiles();
        $this->clearImageCaches();
        $this->optimizeForProduction();
        
        $this->info('✅ Deployment clear completed successfully!');
        
        return Command::SUCCESS;
    }

    private function clearApplicationCaches()
    {
        $this->info('📦 Clearing application caches...');
        
        try {
            Artisan::call('cache:clear');
            $this->line('  ✓ Application cache cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Application cache clear failed: ' . $e->getMessage());
        }

        try {
            Cache::flush();
            $this->line('  ✓ Cache store flushed');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Cache store flush failed: ' . $e->getMessage());
        }
    }

    private function clearViewCaches()
    {
        $this->info('👁 Clearing view caches...');
        
        try {
            Artisan::call('view:clear');
            $this->line('  ✓ View cache cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ View cache clear failed: ' . $e->getMessage());
        }

        // Clear compiled views
        $compiledPath = storage_path('framework/views');
        if (File::exists($compiledPath)) {
            File::deleteDirectory($compiledPath);
            File::makeDirectory($compiledPath, 0755, true);
            $this->line('  ✓ Compiled views cleared');
        }
    }

    private function clearRouteCaches()
    {
        $this->info('🛣 Clearing route caches...');
        
        try {
            Artisan::call('route:clear');
            $this->line('  ✓ Route cache cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Route cache clear failed: ' . $e->getMessage());
        }
    }

    private function clearConfigCaches()
    {
        $this->info('⚙️ Clearing config caches...');
        
        try {
            Artisan::call('config:clear');
            $this->line('  ✓ Config cache cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Config cache clear failed: ' . $e->getMessage());
        }
    }

    private function clearEventCaches()
    {
        $this->info('🎯 Clearing event caches...');
        
        try {
            Artisan::call('event:clear');
            $this->line('  ✓ Event cache cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Event cache clear failed: ' . $e->getMessage());
        }
    }

    private function clearOptimizedFiles()
    {
        $this->info('🔧 Clearing optimized files...');
        
        try {
            Artisan::call('optimize:clear');
            $this->line('  ✓ Optimized files cleared');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Optimize clear failed: ' . $e->getMessage());
        }
    }

    private function clearTemporaryFiles()
    {
        $this->info('🗑 Clearing temporary files...');
        
        // Clear temp directories
        $tempDirs = [
            storage_path('app/temp'),
            storage_path('app/cache'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/testing'),
        ];

        foreach ($tempDirs as $dir) {
            if (File::exists($dir)) {
                File::deleteDirectory($dir);
                File::makeDirectory($dir, 0755, true);
                $this->line("  ✓ Cleared: " . basename($dir));
            }
        }
    }

    private function clearLogFiles()
    {
        $this->info('📝 Clearing log files...');
        
        $logPath = storage_path('logs');
        if (File::exists($logPath)) {
            $files = File::glob($logPath . '/*.log');
            foreach ($files as $file) {
                if (File::size($file) > 0) {
                    File::put($file, '');
                    $this->line('  ✓ Cleared: ' . basename($file));
                }
            }
        }
    }

    private function clearImageCaches()
    {
        $this->info('🖼 Clearing image caches...');
        
        // Clear image optimization cache
        $imageCachePath = storage_path('app/cache/images');
        if (File::exists($imageCachePath)) {
            File::deleteDirectory($imageCachePath);
            $this->line('  ✓ Image cache cleared');
        }

        // Clear thumbnail cache
        $thumbCachePath = public_path('cache/thumbnails');
        if (File::exists($thumbCachePath)) {
            File::deleteDirectory($thumbCachePath);
            $this->line('  ✓ Thumbnail cache cleared');
        }
    }

    private function optimizeForProduction()
    {
        $this->info('⚡ Optimizing for production...');
        
        try {
            // Optimize autoloader
            Artisan::call('optimize');
            $this->line('  ✓ Application optimized');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Optimization failed: ' . $e->getMessage());
        }

        try {
            // Cache configuration
            Artisan::call('config:cache');
            $this->line('  ✓ Config cached');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Config cache failed: ' . $e->getMessage());
        }

        try {
            // Cache routes
            Artisan::call('route:cache');
            $this->line('  ✓ Routes cached');
        } catch (\Exception $e) {
            $this->warn('  ⚠ Route cache failed: ' . $e->getMessage());
        }

        try {
            // Cache views
            Artisan::call('view:cache');
            $this->line('  ✓ Views cached');
        } catch (\Exception $e) {
            $this->warn('  ⚠ View cache failed: ' . $e->getMessage());
        }
    }
}

