<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductCsvImportService;
use App\Services\ProductCsvPreviewService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Laravel\Facades\Image;
use Tests\TestCase;

class ProductCsvImportServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
        DB::table('categories')->insert(['id' => 1, 'name' => 'Men', 'slug' => 'men']);
        DB::table('brands')->insert(['id' => 1, 'name' => 'Avenue', 'slug' => 'avenue']);
    }

    public function test_import_creates_product_resolves_relations_and_unique_sizes(): void
    {
        $result = $this->import("name_ar,name_en,slug_en,category,brand,sku,price,sizes\nمنتج,Product,new-product,Men,Avenue,SKU-1,10,S|M|S\n");

        $product = Product::with('sizes')->sole();
        $this->assertSame(['imported' => 1, 'skipped' => 0, 'images_imported' => 0, 'image_warnings' => 0, 'warnings' => 0, 'failed' => 0], $result['summary']);
        $this->assertSame(1, $product->category_id);
        $this->assertSame(1, $product->brand_id);
        $this->assertSame(['S', 'M'], $product->sizes->pluck('size')->all());
    }

    public function test_unknown_category_blocks_while_unknown_brand_imports_null_with_warning(): void
    {
        $invalid = $this->preview("name_ar,name_en,category,price\nمنتج,Product,Missing,10\n");
        $this->assertSame('INVALID', $invalid['rows'][0]['status']);
        try {
            app(ProductCsvImportService::class)->import($invalid);
            $this->fail('Expected category validation failure.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('products', 0);
        }

        $result = $this->import("name_ar,name_en,category,brand,price\nمنتج,Product,Men,Missing,10\n");
        $this->assertSame(1, $result['summary']['warnings']);
        $this->assertNull(Product::sole()->brand_id);
    }

    public function test_existing_and_in_file_duplicates_are_skipped_without_updates(): void
    {
        Product::create($this->product(['name' => 'Existing', 'slug' => 'existing', 'sku' => 'SKU-OLD', 'price' => 20]));
        $csv = "name_ar,name_en,slug_en,category,sku,price\nمنتج,Changed,existing,Men,SKU-OLD,1\nأول,First,fresh,Men,SKU-FRESH,10\nثان,Second,fresh,Men,SKU-FRESH,99\n";

        $result = $this->import($csv);

        $this->assertSame(1, $result['summary']['imported']);
        $this->assertSame(2, $result['summary']['skipped']);
        $this->assertSame('20.00', Product::where('sku', 'SKU-OLD')->sole()->price);
        $this->assertSame(1, Product::where('slug', 'fresh')->count());
    }

    public function test_persistence_exception_rolls_back_complete_batch(): void
    {
        DB::statement("CREATE TRIGGER fail_second BEFORE INSERT ON products WHEN NEW.name = 'Second' BEGIN SELECT RAISE(ABORT, 'forced failure'); END");
        $this->expectException(QueryException::class);

        try {
            $this->import("name_ar,name_en,slug_en,category,price\nأول,First,first,Men,10\nثان,Second,second,Men,20\n");
        } finally {
            $this->assertDatabaseCount('products', 0);
        }
    }

    public function test_images_attach_in_order_deduplicate_content_and_fall_back_primary(): void
    {
        Storage::fake('public');
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $jpeg = (string) Image::read($png)->toJpeg();
        Http::fake([
            'http://93.184.216.34/bad' => Http::response('<html>bad</html>', 200),
            'http://93.184.216.34/a.png' => Http::response($png, 200),
            'http://93.184.216.34/b.png' => Http::response($png, 200),
            'http://93.184.216.34/c.jpg' => Http::response($jpeg, 200),
        ]);

        $result = $this->import("name_ar,name_en,category,price,primary_image_url,image_urls\nProduct,Product,Men,10,http://93.184.216.34/bad,http://93.184.216.34/a.png|http://93.184.216.34/a.png|http://93.184.216.34/b.png|http://93.184.216.34/c.jpg\n");

        $images = Product::sole()->images;
        $this->assertCount(2, $images);
        $this->assertTrue($images->first()->is_primary);
        $this->assertSame([0, 1], $images->pluck('sort_order')->all());
        $this->assertSame(2, $result['summary']['images_imported']);
        $this->assertSame(1, $result['summary']['image_warnings']);
        Storage::disk('public')->assertExists($images->first()->path);
        Http::assertSentCount(4);
    }

    public function test_all_image_failures_still_import_product(): void
    {
        Storage::fake('public');
        Http::fake(['http://93.184.216.34/*' => Http::response('not-image', 200)]);
        $result = $this->import("name_ar,name_en,category,price,image_urls\nProduct,Product,Men,10,http://93.184.216.34/a|http://93.184.216.34/b\n");
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_images', 0);
        $this->assertSame(2, $result['summary']['image_warnings']);
    }

    public function test_database_rollback_removes_only_new_import_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/existing.webp', 'keep');
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        Http::fake(['http://93.184.216.34/*' => Http::response($png, 200)]);
        DB::statement("CREATE TRIGGER fail_second_image_batch BEFORE INSERT ON products WHEN NEW.name = 'Second' BEGIN SELECT RAISE(ABORT, 'forced failure'); END");

        try {
            $this->import("name_ar,name_en,slug_en,category,price,image_urls\nFirst,First,first-image,Men,10,http://93.184.216.34/a.png\nSecond,Second,second-image,Men,20,\n");
            $this->fail('Expected persistence failure.');
        } catch (QueryException) {
            $this->assertDatabaseCount('products', 0);
            Storage::disk('public')->assertExists('products/existing.webp');
            $this->assertCount(1, Storage::disk('public')->allFiles('products'));
        }
    }

    public function test_confirm_reparses_csv_ignores_payload_and_cleans_temporary_file(): void
    {
        Storage::fake('local');
        $user = $this->user(['platform.index' => true]);
        $csv = UploadedFile::fake()->createWithContent('products.csv', "name_ar,name_en,slug_en,category,price\nمنتج,Server Name,server-name,Men,10\n");

        $this->actingAs($user)->post(route('platform.products.import', ['method' => 'preview']), ['csv' => $csv]);
        $token = session('product_import_token');
        $path = "product-imports/{$user->id}/{$token}.csv";
        Storage::disk('local')->assertExists($path);

        $this->post(route('platform.products.import', ['method' => 'confirmImport']), ['name' => 'Tampered Name'])
            ->assertRedirect(route('platform.products.import'));

        $this->assertSame('Server Name', Product::sole()->getRawOriginal('name'));
        Storage::disk('local')->assertMissing($path);
    }

    public function test_guest_and_user_without_permission_cannot_confirm(): void
    {
        $this->post(route('platform.products.import', ['method' => 'confirmImport']))->assertRedirect();
        $this->actingAs($this->user([]))
            ->post(route('platform.products.import', ['method' => 'confirmImport']))
            ->assertForbidden();
    }

    private function import(string $csv): array
    {
        return app(ProductCsvImportService::class)->import($this->preview($csv));
    }

    private function preview(string $csv): array
    {
        $path = tempnam(sys_get_temp_dir(), 'avenue-import-');
        file_put_contents($path, $csv);
        try {
            return app(ProductCsvPreviewService::class)->preview($path);
        } finally {
            @unlink($path);
        }
    }

    private function user(array $permissions): User
    {
        return User::create(['name' => 'Importer', 'email' => uniqid().'@example.test', 'password' => bcrypt('password'), 'permissions' => $permissions]);
    }

    private function product(array $overrides = []): array
    {
        return array_merge(['category_id' => 1, 'name' => 'Product', 'slug' => 'product', 'price' => 10, 'stock_qty' => 0,
            'is_active' => true, 'is_featured' => false, 'published_at' => now()], $overrides);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $t): void { $t->id(); $t->string('name'); $t->string('email'); $t->string('password'); $t->json('permissions')->nullable(); $t->rememberToken(); $t->timestamps(); });
        Schema::create('roles', function (Blueprint $t): void { $t->increments('id'); $t->string('slug'); $t->string('name'); $t->json('permissions')->nullable(); $t->timestamps(); });
        Schema::create('role_users', function (Blueprint $t): void { $t->unsignedBigInteger('user_id'); $t->unsignedInteger('role_id'); $t->primary(['user_id', 'role_id']); });
        Schema::create('categories', function (Blueprint $t): void { $t->id(); $t->string('name'); $t->string('name_ar')->nullable(); $t->string('name_en')->nullable(); $t->string('slug')->unique(); $t->string('slug_ar')->nullable(); $t->string('slug_en')->nullable(); $t->timestamps(); });
        Schema::create('brands', function (Blueprint $t): void { $t->id(); $t->string('name'); $t->string('name_ar')->nullable(); $t->string('name_en')->nullable(); $t->string('slug')->unique(); $t->string('slug_ar')->nullable(); $t->string('slug_en')->nullable(); $t->timestamps(); });
        Schema::create('products', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('category_id')->nullable(); $t->unsignedBigInteger('brand_id')->nullable();
            $t->string('name'); $t->string('slug')->unique(); $t->text('short_description')->nullable(); $t->text('description')->nullable();
            foreach (['name_en','name_ar','slug_en','slug_ar','short_description_en','short_description_ar','description_en','description_ar','seo_title_ar','seo_title_en','seo_description_ar','seo_description_en'] as $c) $t->text($c)->nullable();
            $t->string('sku')->nullable()->unique(); $t->decimal('price', 10, 2); $t->decimal('sale_price', 10, 2)->nullable();
            $t->unsignedInteger('stock_qty')->default(0); $t->boolean('is_active')->default(true); $t->boolean('is_featured')->default(false); $t->timestamp('published_at')->nullable(); $t->timestamps();
        });
        Schema::create('product_sizes', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('product_id'); $t->string('size'); $t->decimal('price', 10, 2)->nullable(); $t->decimal('sale_price', 10, 2)->nullable(); $t->unsignedInteger('stock_qty')->default(0); $t->string('sku')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->boolean('is_active')->default(true); $t->timestamps(); });
        Schema::create('product_images', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('product_id'); $t->string('path'); $t->string('alt')->nullable(); $t->boolean('is_primary')->default(false); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('settings', function (Blueprint $t): void { $t->id(); $t->string('key')->unique(); $t->text('value')->nullable(); $t->string('group')->nullable(); $t->timestamps(); });
        Schema::create('notifications', function (Blueprint $t): void { $t->uuid('id')->primary(); $t->string('type'); $t->morphs('notifiable'); $t->text('data'); $t->timestamp('read_at')->nullable(); $t->timestamps(); });
    }
}
