<?php

namespace Tests\Feature;

use App\Services\EnhancedPerformanceService;
use App\Support\SeoData;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TechnicalSeoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('settings', function (Blueprint $t): void { $t->id(); $t->string('key')->unique(); $t->text('value')->nullable(); $t->string('group')->nullable(); $t->timestamps(); });
        Schema::create('products', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('category_id')->nullable(); $t->string('slug')->unique(); $t->string('slug_ar')->nullable(); $t->string('slug_en')->nullable(); $t->boolean('is_active'); $t->timestamp('published_at')->nullable(); $t->timestamps(); });
        Schema::create('categories', function (Blueprint $t): void { $t->id(); $t->string('slug')->unique(); $t->string('slug_ar')->nullable(); $t->string('slug_en')->nullable(); $t->boolean('is_active'); $t->timestamps(); });
        Schema::create('brands', function (Blueprint $t): void { $t->id(); $t->string('slug')->unique(); $t->string('slug_ar')->nullable(); $t->string('slug_en')->nullable(); $t->boolean('is_active'); $t->timestamps(); });
        Schema::create('product_images', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('product_id'); $t->string('path'); $t->string('alt')->nullable(); $t->boolean('is_primary')->default(false); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('product_sizes', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('product_id'); $t->string('size'); $t->boolean('is_active')->default(true); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
    }

    public function test_product_page_emits_explicit_seo_valid_jsonld_and_one_canonical(): void
    {
        \DB::table('products')->insert(['slug' => 'seo-product', 'slug_en' => 'seo-product', 'slug_ar' => 'seo-product-ar', 'is_active' => 1, 'published_at' => null, 'created_at' => now(), 'updated_at' => now()]);
        $product = $this->product(['seo_title_en' => 'Explicit SEO Title', 'seo_description_en' => 'Explicit SEO description.']);
        $this->mock(EnhancedPerformanceService::class, function ($mock) use ($product): void {
            $mock->shouldReceive('getCachedProductDetails')->once()->andReturn($product);
            $mock->shouldReceive('getCachedRelatedProducts')->once()->andReturn([]);
        });

        $response = $this->get(route('products.show', 'seo-product'))->assertOk();
        $html = $response->getContent();
        $response->assertSee('<title>Explicit SEO Title</title>', false)
            ->assertSee('content="Explicit SEO description."', false)
            ->assertSee('rel="canonical" href="'.route('products.show', 'seo-product').'"', false);
        $this->assertSame(1, substr_count($html, 'rel="canonical"'));

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
        $this->assertNotEmpty($matches[1]);
        foreach ($matches[1] as $json) $this->assertIsArray(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
        $productSchema = json_decode($matches[1][0], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('Product', $productSchema['@type']);
        $this->assertArrayNotHasKey('sku', $productSchema);
        $this->assertArrayNotHasKey('brand', $productSchema);
    }

    public function test_product_seo_falls_back_to_name_and_short_description(): void
    {
        $seo = SeoData::product((object) $this->product());
        $this->assertSame('SEO Product', $seo['title']);
        $this->assertSame('Factual short description.', $seo['description']);
    }

    public function test_localized_product_routes_hreflang_direction_switcher_and_legacy_redirect(): void
    {
        \DB::table('products')->insert(['slug' => 'base-slug', 'slug_en' => 'product-en', 'slug_ar' => 'product-ar', 'is_active' => 1, 'published_at' => null, 'created_at' => now(), 'updated_at' => now()]);
        $this->mock(EnhancedPerformanceService::class, function ($mock): void {
            $mock->shouldReceive('getCachedProductDetails')->twice()->with('base-slug')->andReturnUsing(function () {
                return $this->product([
                    'slug' => app()->getLocale() === 'ar' ? 'product-ar' : 'product-en',
                    'slug_ar' => 'product-ar', 'slug_en' => 'product-en',
                    'name' => app()->getLocale() === 'ar' ? 'منتج عربي' : 'English Product',
                ]);
            });
            $mock->shouldReceive('getCachedRelatedProducts')->twice()->andReturn([]);
        });

        $arUrl = route('products.show', ['locale' => 'ar', 'slug' => 'product-ar']);
        $enUrl = route('products.show', ['locale' => 'en', 'slug' => 'product-en']);
        $ar = $this->get($arUrl)->assertOk();
        $ar->assertSee('<html lang="ar" dir="rtl"', false)
            ->assertSee('rel="canonical" href="'.$arUrl.'"', false)
            ->assertSee('hreflang="en" href="'.$enUrl.'"', false)
            ->assertSee('hreflang="x-default" href="'.$enUrl.'"', false)
            ->assertSee('href="'.$enUrl.'"', false);

        $this->get($enUrl)->assertOk()
            ->assertSee('<html lang="en" dir="ltr"', false)
            ->assertSee('rel="canonical" href="'.$enUrl.'"', false)
            ->assertSee('hreflang="ar" href="'.$arUrl.'"', false);

        $this->get('/product/base-slug')->assertRedirect($enUrl)->assertStatus(301);
        $this->get('/fr/product/product-en')->assertNotFound();
    }

    public function test_localized_search_remains_noindex_and_api_is_not_locale_redirected(): void
    {
        $this->mock(EnhancedPerformanceService::class, function ($mock): void {
            $mock->shouldReceive('getCachedProducts')->once()->andReturn(['data' => new LengthAwarePaginator([], 0, 12), 'pagination' => ['total' => 0, 'current_page' => 1, 'last_page' => 1]]);
            $mock->shouldReceive('getCachedSearchSuggestions')->once()->andReturn([]);
            $mock->shouldReceive('getCachedNavigation')->once()->andReturn(['brands' => [], 'categories' => []]);
        });
        $this->get(route('search', ['locale' => 'ar', 'q' => 'shirt']))->assertOk()->assertSee('content="noindex, follow"', false);
        $this->get('/api/home/settings')->assertOk()->assertHeaderMissing('Location');
        $admin = $this->get('/admin');
        $this->assertNotSame(301, $admin->getStatusCode());
        $this->assertStringNotContainsString('/en/admin', (string) $admin->headers->get('Location'));
    }

    public function test_search_and_wishlist_are_noindex(): void
    {
        $this->assertStringContainsString("@section('meta_robots', 'noindex, follow')", file_get_contents(resource_path('views/search/results.blade.php')));
        $this->get(route('wishlist.index'))->assertOk()->assertSee('content="noindex, follow"', false);
    }

    public function test_sitemap_excludes_inactive_records_and_uses_real_timestamps(): void
    {
        $updated = Carbon::parse('2025-02-03 04:05:06');
        \DB::table('products')->insert([
            ['slug' => 'active-product', 'is_active' => 1, 'published_at' => null, 'created_at' => $updated, 'updated_at' => $updated],
            ['slug' => 'inactive-product', 'is_active' => 0, 'published_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
        \DB::table('categories')->insert(['slug' => 'active-category', 'is_active' => 1, 'created_at' => $updated, 'updated_at' => $updated]);
        \DB::table('brands')->insert([
            ['slug' => 'active-brand', 'is_active' => 1, 'created_at' => $updated, 'updated_at' => $updated],
            ['slug' => 'inactive-brand', 'is_active' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->get(route('sitemap'))->assertOk();
        $response->assertSee('active-product')->assertDontSee('inactive-product')->assertSee('active-brand')->assertDontSee('inactive-brand');
        $response->assertSee('/ar/product/active-product', false)->assertSee('/en/product/active-product', false);
        $this->assertStringNotContainsString('<loc>'.url('/product/active-product').'</loc>', $response->getContent());
        $response->assertSee($updated->toW3cString(), false);
        $this->assertSame(6, substr_count($response->getContent(), '<lastmod>'));
    }

    public function test_robots_references_public_sitemap(): void
    {
        $this->assertStringContainsString('Sitemap: https://avenuebrand.online/sitemap.xml', file_get_contents(public_path('robots.txt')));
    }

    private function product(array $overrides = []): array
    {
        return array_merge([
            'id' => 1, 'name' => 'SEO Product', 'slug' => 'seo-product', 'slug_en' => 'seo-product', 'slug_ar' => 'seo-product-ar',
            'short_description' => 'Factual short description.', 'description' => 'Factual description.',
            'seo_title_ar' => null, 'seo_title_en' => null, 'seo_description_ar' => null, 'seo_description_en' => null,
            'sku' => null, 'price' => '20.00', 'sale_price' => null, 'effective_price' => '20.00',
            'stock_qty' => 2, 'is_featured' => false, 'published_at' => null,
            'primary_image_url' => 'https://avenue.test/storage/product.webp',
            'images' => [], 'category' => null, 'brand' => null, 'offers' => [],
        ], $overrides);
    }
}
