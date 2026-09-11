<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Slide;
use App\Models\Offer;
use App\Models\ProductImage;
use App\Services\ImageService;

class BackendImageManagement extends Command
{
    protected $signature = 'backend:images {action} {--model=} {--id=}';
    protected $description = 'Backend image management commands';

    public function handle()
    {
        $action = $this->argument('action');
        $model = $this->option('model');
        $id = $this->option('id');

        switch ($action) {
            case 'list':
                $this->listImages($model);
                break;
            case 'check':
                $this->checkImages($model, $id);
                break;
            case 'fix':
                $this->fixImages($model, $id);
                break;
            case 'stats':
                $this->showStats();
                break;
            default:
                $this->error('Invalid action. Use: list, check, fix, or stats');
        }

        return Command::SUCCESS;
    }

    private function listImages($model = null)
    {
        $this->info('=== IMAGE LISTING ===');

        if (!$model || $model === 'categories') {
            $this->info("\n--- CATEGORIES ---");
            $categories = Category::whereNotNull('image_path')->get();
            foreach ($categories as $category) {
                $this->line("ID: {$category->id} | Name: {$category->name} | Path: {$category->image_path} | URL: {$category->image_url}");
            }
        }

        if (!$model || $model === 'products') {
            $this->info("\n--- PRODUCTS ---");
            $products = Product::with('images')->get();
            foreach ($products as $product) {
                $this->line("ID: {$product->id} | Name: {$product->name} | Primary: {$product->primary_image_url}");
                foreach ($product->images as $image) {
                    $this->line("  └─ Image: {$image->path} | URL: {$image->url}");
                }
            }
        }

        if (!$model || $model === 'brands') {
            $this->info("\n--- BRANDS ---");
            $brands = Brand::whereNotNull('logo_path')->get();
            foreach ($brands as $brand) {
                $this->line("ID: {$brand->id} | Name: {$brand->name} | Path: {$brand->logo_path} | URL: {$brand->logo_url}");
            }
        }
    }

    private function checkImages($model = null, $id = null)
    {
        $this->info('=== IMAGE CHECKING ===');
        $imageService = app(ImageService::class);
        $broken = 0;
        $total = 0;

        if (!$model || $model === 'categories') {
            $this->info("\n--- CHECKING CATEGORIES ---");
            $categories = $id ? Category::where('id', $id)->get() : Category::whereNotNull('image_path')->get();
            foreach ($categories as $category) {
                $total++;
                if (!$imageService->exists($category->image_path)) {
                    $broken++;
                    $this->error("✗ Category {$category->name}: {$category->image_path}");
                } else {
                    $this->line("✓ Category {$category->name}: {$category->image_path}");
                }
            }
        }

        if (!$model || $model === 'products') {
            $this->info("\n--- CHECKING PRODUCTS ---");
            $products = $id ? Product::where('id', $id)->with('images')->get() : Product::with('images')->get();
            foreach ($products as $product) {
                foreach ($product->images as $image) {
                    $total++;
                    if (!$imageService->exists($image->path)) {
                        $broken++;
                        $this->error("✗ Product {$product->name} Image: {$image->path}");
                    } else {
                        $this->line("✓ Product {$product->name} Image: {$image->path}");
                    }
                }
            }
        }

        $this->info("\n=== RESULTS ===");
        $this->info("Total: {$total}");
        $this->info("Broken: {$broken}");
        $this->info("Working: " . ($total - $broken));
    }

    private function fixImages($model = null, $id = null)
    {
        $this->info('=== FIXING IMAGES ===');
        $imageService = app(ImageService::class);
        $fixed = 0;

        if (!$model || $model === 'categories') {
            $this->info("\n--- FIXING CATEGORIES ---");
            $categories = $id ? Category::where('id', $id)->get() : Category::whereNotNull('image_path')->get();
            foreach ($categories as $category) {
                if (!$imageService->exists($category->image_path)) {
                    $this->warn("Fixing category {$category->name}: {$category->image_path}");
                    $category->image_path = null;
                    $category->save();
                    $fixed++;
                }
            }
        }

        if (!$model || $model === 'products') {
            $this->info("\n--- FIXING PRODUCTS ---");
            $products = $id ? Product::where('id', $id)->with('images')->get() : Product::with('images')->get();
            foreach ($products as $product) {
                foreach ($product->images as $image) {
                    if (!$imageService->exists($image->path)) {
                        $this->warn("Fixing product {$product->name} image: {$image->path}");
                        $image->delete();
                        $fixed++;
                    }
                }
            }
        }

        $this->info("\n=== FIXED ===");
        $this->info("Fixed: {$fixed} broken image references");
    }

    private function showStats()
    {
        $this->info('=== IMAGE STATISTICS ===');

        $categories = Category::whereNotNull('image_path')->count();
        $products = Product::withCount('images')->get()->sum('images_count');
        $brands = Brand::whereNotNull('logo_path')->count();
        $slides = Slide::whereNotNull('image_path')->count();
        $offers = Offer::whereNotNull('banner_image')->count();

        $this->table(
            ['Model', 'With Images', 'Total Records'],
            [
                ['Categories', $categories, Category::count()],
                ['Products', $products, Product::count()],
                ['Brands', $brands, Brand::count()],
                ['Slides', $slides, Slide::count()],
                ['Offers', $offers, Offer::count()],
            ]
        );
    }
}
