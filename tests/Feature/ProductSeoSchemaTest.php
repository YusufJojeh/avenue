<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductSeoSchemaTest extends TestCase
{
    public function test_seo_migration_adds_nullable_product_fields(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
        });

        $migration = require database_path('migrations/2026_09_12_000000_add_seo_fields_to_products_table.php');
        $migration->up();

        foreach (['seo_title_ar', 'seo_title_en', 'seo_description_ar', 'seo_description_en'] as $column) {
            $this->assertTrue(Schema::hasColumn('products', $column));
        }

        $product = new Product();
        $product->fill([
            'seo_title_ar' => 'عنوان',
            'seo_title_en' => 'Title',
            'seo_description_ar' => 'وصف',
            'seo_description_en' => 'Description',
        ]);
        $this->assertSame('Title', $product->seo_title_en);

        $migration->down();
        $this->assertFalse(Schema::hasColumn('products', 'seo_title_en'));
    }
}
