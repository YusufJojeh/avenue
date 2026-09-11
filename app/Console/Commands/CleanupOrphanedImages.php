<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupOrphanedImages extends Command
{
    protected $signature = 'images:cleanup-orphaned';
    protected $description = 'Clean up orphaned image records from database';

    public function handle()
    {
        $this->info('Starting orphaned image cleanup...');

        $orphanedCount = 0;
        $deletedCount = 0;

        // Get all product images
        $images = ProductImage::all();

        foreach ($images as $image) {
            if (!$image->path) {
                continue;
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($image->path)) {
                $this->warn("Orphaned image found: {$image->path}");
                $orphanedCount++;

                // Ask for confirmation before deletion
                if ($this->confirm("Delete orphaned record for product {$image->product_id}?")) {
                    $image->delete();
                    $deletedCount++;
                    $this->info("Deleted orphaned record: {$image->path}");
                }
            }
        }

        $this->info("Cleanup completed:");
        $this->info("- Orphaned images found: {$orphanedCount}");
        $this->info("- Records deleted: {$deletedCount}");

        return 0;
    }
}
