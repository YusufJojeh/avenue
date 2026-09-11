<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Slide;
use App\Models\Offer;
use App\Services\ImageService;

class TestAllImages extends Command
{
    protected $signature = 'images:test-all';
    protected $description = 'Test all image URLs and functionality';

    public function handle()
    {
        $this->info('Testing all image functionality...');

        $imageService = app(ImageService::class);
        $total = 0;
        $working = 0;
        $broken = 0;

        // Test Products
        $this->info('Testing Product Images...');
        $products = Product::with('images')->get();
        foreach ($products as $product) {
            $total++;
            $url = $product->primary_image_url;
            if ($this->testImageUrl($url)) {
                $working++;
                $this->line("✓ Product {$product->name}: {$url}");
            } else {
                $broken++;
                $this->error("✗ Product {$product->name}: {$url}");
            }
        }

        // Test Categories
        $this->info('Testing Category Images...');
        $categories = Category::all();
        foreach ($categories as $category) {
            $total++;
            $url = $category->image_url;
            if ($this->testImageUrl($url)) {
                $working++;
                $this->line("✓ Category {$category->name}: {$url}");
            } else {
                $broken++;
                $this->error("✗ Category {$category->name}: {$url}");
            }
        }

        // Test Brands
        $this->info('Testing Brand Logos...');
        $brands = Brand::all();
        foreach ($brands as $brand) {
            $total++;
            $url = $brand->logo_url;
            if ($this->testImageUrl($url)) {
                $working++;
                $this->line("✓ Brand {$brand->name}: {$url}");
            } else {
                $broken++;
                $this->error("✗ Brand {$brand->name}: {$url}");
            }
        }

        // Test Slides
        $this->info('Testing Slide Images...');
        $slides = Slide::all();
        foreach ($slides as $slide) {
            $total++;
            $url = $slide->image_url;
            if ($this->testImageUrl($url)) {
                $working++;
                $this->line("✓ Slide {$slide->title}: {$url}");
            } else {
                $broken++;
                $this->error("✗ Slide {$slide->title}: {$url}");
            }
        }

        // Test Offers
        $this->info('Testing Offer Banners...');
        $offers = Offer::all();
        foreach ($offers as $offer) {
            $total++;
            $url = $offer->banner_url;
            if ($this->testImageUrl($url)) {
                $working++;
                $this->line("✓ Offer {$offer->title}: {$url}");
            } else {
                $broken++;
                $this->error("✗ Offer {$offer->title}: {$url}");
            }
        }

        $this->info("\n=== IMAGE TEST RESULTS ===");
        $this->info("Total: {$total}");
        $this->info("Working: {$working}");
        $this->info("Broken: {$broken}");

        if ($broken > 0) {
            $this->warn("Run 'php artisan images:fix-all' to fix broken images");
        }

        return Command::SUCCESS;
    }

    private function testImageUrl(string $url): bool
    {
        try {
            $headers = get_headers($url, 1);
            return strpos($headers[0], '200') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
