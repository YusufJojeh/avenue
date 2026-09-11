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

class FixAllImages extends Command
{
    protected $signature = 'images:fix-all';
    protected $description = 'Fix all image-related issues in the application';

    public function handle()
    {
        $this->info('Starting comprehensive image fix...');

        $imageService = app(ImageService::class);
        $fixed = 0;
        $errors = 0;

        // Fix Product Images
        $this->info('Checking Product Images...');
        $products = Product::with('images')->get();
        foreach ($products as $product) {
            foreach ($product->images as $image) {
                if ($image->path && !$imageService->exists($image->path)) {
                    $this->warn("Orphaned product image: {$image->path}");
                    $image->delete();
                    $fixed++;
                }
            }
        }

        // Fix Categories
        $this->info('Checking Categories...');
        $categories = Category::whereNotNull('image_path')->get();
        foreach ($categories as $category) {
            if (!$imageService->exists($category->image_path)) {
                $this->warn("Orphaned category image: {$category->image_path}");
                $category->image_path = null;
                $category->save();
                $fixed++;
            }
        }

        // Fix Brands
        $this->info('Checking Brands...');
        $brands = Brand::whereNotNull('logo_path')->get();
        foreach ($brands as $brand) {
            if (!$imageService->exists($brand->logo_path)) {
                $this->warn("Orphaned brand logo: {$brand->logo_path}");
                $brand->logo_path = null;
                $brand->save();
                $fixed++;
            }
        }

        // Fix Slides
        $this->info('Checking Slides...');
        $slides = Slide::whereNotNull('image_path')->get();
        foreach ($slides as $slide) {
            if (!$imageService->exists($slide->image_path)) {
                $this->warn("Orphaned slide image: {$slide->image_path}");
                $slide->image_path = null;
                $slide->save();
                $fixed++;
            }
        }

        // Fix Offers
        $this->info('Checking Offers...');
        $offers = Offer::whereNotNull('banner_image')->get();
        foreach ($offers as $offer) {
            if (!$imageService->exists($offer->banner_image)) {
                $this->warn("Orphaned offer banner: {$offer->banner_image}");
                $offer->banner_image = null;
                $offer->save();
                $fixed++;
            }
        }

        $this->info("Image fix completed!");
        $this->info("Fixed: {$fixed} orphaned image references");

        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }

        return Command::SUCCESS;
    }
}
