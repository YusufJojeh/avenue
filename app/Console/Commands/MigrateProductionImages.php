<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Services\ImageService;

class MigrateProductionImages extends Command
{
    protected $signature = 'images:migrate-production {--dry-run : Show what would be done without making changes}';
    protected $description = 'Migrate production images to local storage';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Checking for production images that need migration...');

        $imageService = app(ImageService::class);
        $migrated = 0;
        $errors = 0;

        // Check for the specific image you mentioned
        $specificImage = 'categories/chatgpt-image-1-aghsts-2025-04-21-00-s-2025-10-19-23-11-29-gihxkwll_2025-10-19_23-56-59_cXlvm0YY.png';

        $category = Category::where('image_path', $specificImage)->first();
        if ($category) {
            $this->warn("Found category with production image: {$category->name}");
            $this->warn("Image path: {$category->image_path}");

            if (!$imageService->exists($category->image_path)) {
                $this->error("Image file does not exist locally: {$category->image_path}");

                if (!$dryRun) {
                    // Clear the image path to use fallback
                    $category->image_path = null;
                    $category->save();
                    $this->info("Cleared image path for category: {$category->name}");
                    $migrated++;
                } else {
                    $this->info("Would clear image path for category: {$category->name}");
                }
            } else {
                $this->info("Image exists locally: {$category->image_path}");
            }
        } else {
            $this->info("No category found with the specific production image path");
        }

        // Check all categories for missing images
        $categories = Category::whereNotNull('image_path')->get();
        foreach ($categories as $category) {
            if (!$imageService->exists($category->image_path)) {
                $this->warn("Missing image for category {$category->name}: {$category->image_path}");

                if (!$dryRun) {
                    $category->image_path = null;
                    $category->save();
                    $migrated++;
                } else {
                    $this->info("Would clear image path for category: {$category->name}");
                }
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN COMPLETE - {$migrated} images would be cleared");
        } else {
            $this->info("Migration complete!");
            $this->info("Cleared: {$migrated} missing image references");
        }

        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }

        return Command::SUCCESS;
    }
}
