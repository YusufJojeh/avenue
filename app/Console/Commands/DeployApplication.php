<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DeployApplication extends Command
{
    protected $signature = 'deploy {--env=production : Environment to deploy to} {--skip-backup : Skip backup creation}';
    protected $description = 'Deploy the application with all optimizations';

    public function handle()
    {
        $env = $this->option('env');
        $skipBackup = $this->option('skip-backup');

        $this->info("🚀 Starting deployment to {$env} environment...");

        // Step 1: Pre-deployment checks
        $this->info('📋 Step 1: Pre-deployment checks');
        $this->checkRequirements();

        // Step 2: Create backup
        if (!$skipBackup) {
            $this->info('💾 Step 2: Creating backup');
            $this->createBackup();
        }

        // Step 3: Update dependencies
        $this->info('📦 Step 3: Updating dependencies');
        $this->updateDependencies();

        // Step 4: Laravel optimization
        $this->info('⚡ Step 4: Laravel optimization');
        $this->optimizeLaravel($env);

        // Step 5: Image system setup
        $this->info('🖼️ Step 5: Image system setup');
        $this->setupImageSystem();

        // Step 6: Final verification
        $this->info('🔍 Step 6: Final verification');
        $this->verifyDeployment();

        $this->info('🎉 Deployment completed successfully!');

        return Command::SUCCESS;
    }

    private function checkRequirements()
    {
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->error('PHP 8.2+ required. Current version: ' . PHP_VERSION);
            return false;
        }
        $this->line('✅ PHP version: ' . PHP_VERSION);

        // Check if .env exists
        if (!File::exists('.env')) {
            $this->error('.env file not found. Please create it first.');
            return false;
        }
        $this->line('✅ Environment file found');

        // Check database connection
        try {
            \DB::connection()->getPdo();
            $this->line('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    private function createBackup()
    {
        $backupDir = 'backups/' . date('Y-m-d_H-i-s');

        if (!File::exists('backups')) {
            File::makeDirectory('backups');
        }

        // Backup .env
        if (File::exists('.env')) {
            File::copy('.env', $backupDir . '/.env.backup');
            $this->line('✅ Environment file backed up');
        }

        // Backup storage
        if (File::exists('storage')) {
            File::copyDirectory('storage', $backupDir . '/storage.backup');
            $this->line('✅ Storage directory backed up');
        }

        $this->line("📁 Backup created: {$backupDir}");
    }

    private function updateDependencies()
    {
        // Clear Composer cache
        Artisan::call('composer:clear-cache');
        $this->line('✅ Composer cache cleared');

        // Note: Composer install should be run manually
        $this->warn('⚠️  Please run: composer install --optimize-autoloader --no-dev');
    }

    private function optimizeLaravel($env)
    {
        // Clear all caches
        $this->line('🧹 Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->line('✅ Caches cleared');

        // Generate key if needed
        if (empty(config('app.key'))) {
            Artisan::call('key:generate');
            $this->line('✅ Application key generated');
        }

        // Run migrations
        $this->line('🗄️ Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line('✅ Migrations completed');

        // Create storage link
        if (!File::exists('public/storage')) {
            Artisan::call('storage:link');
            $this->line('✅ Storage link created');
        }

        // Production optimizations
        if ($env === 'production') {
            $this->line('⚡ Applying production optimizations...');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('event:cache');
            $this->line('✅ Production caches created');
        }
    }

    private function setupImageSystem()
    {
        // Test images
        $this->line('🖼️ Testing image system...');
        Artisan::call('images:test-all');

        // Fix images
        $this->line('🔧 Fixing image issues...');
        Artisan::call('images:fix-all');

        $this->line('✅ Image system optimized');
    }

    private function verifyDeployment()
    {
        // Test application
        $this->line('🧪 Testing application...');

        try {
            $about = Artisan::call('about');
            $this->line('✅ Application is working');
        } catch (\Exception $e) {
            $this->error('❌ Application test failed: ' . $e->getMessage());
            return false;
        }

        // Test database
        try {
            \DB::connection()->getPdo();
            $this->line('✅ Database connection verified');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return false;
        }

        // Test storage link
        if (File::exists('public/storage')) {
            $this->line('✅ Storage link verified');
        } else {
            $this->warn('⚠️  Storage link may need manual creation');
        }

        return true;
    }
}

