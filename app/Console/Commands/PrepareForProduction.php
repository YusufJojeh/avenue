<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PrepareForProduction extends Command
{
    protected $signature = 'production:prepare';
    protected $description = 'Prepare the application for production deployment';

    public function handle()
    {
        $this->info('Starting production preparation...');

        // Clear all caches
        $this->info('Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        // Optimize autoloader
        $this->info('Optimizing autoloader...');
        exec('composer dump-autoload --optimize');

        // Cache configuration
        $this->info('Caching configuration...');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        // Link storage
        $this->info('Linking storage...');
        Artisan::call('storage:link');

        // Run performance optimizations
        $this->info('Running performance optimizations...');
        Artisan::call('model:prune');
        Artisan::call('optimize');

        $this->info('Production preparation completed successfully!');
        $this->info('Remember to:');
        $this->line('1. Update APP_ENV to production in .env');
        $this->line('2. Set APP_DEBUG to false in .env');
        $this->line('3. Set strong passwords for admin accounts');
        $this->line('4. Configure proper mail settings');
        $this->line('5. Set up proper cache driver (Redis recommended)');
    }
}
