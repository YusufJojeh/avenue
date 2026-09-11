<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageAccessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_image_accessor_does_not_query_when_images_are_loaded(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/secondary.webp', 'secondary');
        Storage::disk('public')->put('products/primary.webp', 'primary');

        $product = Product::create([
            'name' => 'Performance Product',
            'slug' => 'performance-product',
            'price' => 10,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/secondary.webp',
            'sort_order' => 0,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/primary.webp',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $product->load('images');
        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $url = $product->primary_image_url;

        $this->assertStringContainsString('products/primary.webp', $url);
        $this->assertSame([], $queries);
    }
}
