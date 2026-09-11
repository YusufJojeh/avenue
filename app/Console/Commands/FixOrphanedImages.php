<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FixOrphanedImages extends Command
{
    protected $signature = 'images:fix-orphaned';
    protected $description = 'Fix orphaned image records in database';

    public function handle()
    {
        $this->info('Checking for orphaned image records...');

        $orphanedCount = 0;
        $fixedCount = 0;

        // Get all product images
        $images = ProductImage::all();

        foreach ($images as $image) {
            if (!$image->path) {
                $this->warn("Image {$image->id} has no path - deleting");
                $image->delete();
                $fixedCount++;
                continue;
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($image->path)) {
                $this->warn("Orphaned image found: {$image->path}");
                $orphanedCount++;

                // Delete the orphaned record
                $image->delete();
                $fixedCount++;
                $this->info("Deleted orphaned record: {$image->path}");
            }
        }

        $this->info("Cleanup completed:");
        $this->info("- Orphaned images found: {$orphanedCount}");
        $this->info("- Records fixed: {$fixedCount}");

        return 0;
    }
}
