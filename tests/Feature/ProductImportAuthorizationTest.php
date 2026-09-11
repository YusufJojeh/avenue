<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ProductCsvContract;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductImportAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email'); $table->string('password');
            $table->json('permissions')->nullable(); $table->rememberToken(); $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table): void {
            $table->increments('id'); $table->string('slug'); $table->string('name');
            $table->json('permissions')->nullable(); $table->timestamps();
        });
        Schema::create('role_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id'); $table->unsignedInteger('role_id');
            $table->primary(['user_id', 'role_id']);
        });
        Schema::create('categories', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('name_ar')->nullable(); $table->string('name_en')->nullable();
            $table->string('slug'); $table->string('slug_ar')->nullable(); $table->string('slug_en')->nullable();
            $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('brands', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('name_ar')->nullable(); $table->string('name_en')->nullable();
            $table->string('slug'); $table->string('slug_ar')->nullable(); $table->string('slug_en')->nullable(); $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('category_id')->nullable();
            $table->boolean('is_active')->default(true); $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable(); $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table): void {
            $table->id(); $table->string('key')->unique(); $table->text('value')->nullable();
            $table->string('group')->nullable(); $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary(); $table->string('type');
            $table->morphs('notifiable'); $table->text('data');
            $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
    }

    public function test_user_without_platform_permission_cannot_access_importer(): void
    {
        $user = User::create([
            'name' => 'Unauthorized', 'email' => 'unauthorized@example.test',
            'password' => bcrypt('password'), 'permissions' => [],
        ]);

        $this->actingAs($user)->get(route('platform.products.import'))->assertForbidden();
    }

    public function test_authorized_user_can_preview_without_product_writes(): void
    {
        $user = User::create([
            'name' => 'Catalog Admin', 'email' => 'catalog@example.test',
            'password' => bcrypt('password'), 'permissions' => ['platform.index' => true],
        ]);
        \DB::table('categories')->insert(['name' => 'Men', 'slug' => 'men']);
        $csv = UploadedFile::fake()->createWithContent('products.csv', "name_ar,name_en,category,price\nمنتج,Product,Men,10\n");

        $this->actingAs($user)->get(route('platform.products.import'))->assertOk();

        $this->actingAs($user)
            ->post(route('platform.products.import', ['method' => 'preview']), ['csv' => $csv])
            ->assertRedirect(route('platform.products.import'))
            ->assertSessionHas('product_import_preview.summary.valid', 1);

        $this->get(route('platform.products.import'))->assertOk()->assertSee('Ready: 1');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_template_download_requires_permission_and_uses_contract(): void
    {
        $route = route('platform.products.import', ['method' => 'downloadTemplate']);
        $this->post($route)->assertRedirect();

        $unauthorized = User::create(['name' => 'No Access', 'email' => 'no-template@example.test', 'password' => bcrypt('password'), 'permissions' => []]);
        $this->actingAs($unauthorized)->post($route)->assertForbidden();

        $authorized = User::create(['name' => 'Importer', 'email' => 'template@example.test', 'password' => bcrypt('password'), 'permissions' => ['platform.index' => true]]);
        $response = $this->actingAs($authorized)->post($route)->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertSame(ProductCsvContract::template(), $response->getContent());
        $this->assertDatabaseCount('products', 0);
    }

    public function test_chatgpt_prompt_download_requires_permission_and_uses_canonical_headers(): void
    {
        $route = route('platform.products.import', ['method' => 'downloadChatGptPrompt']);
        $this->post($route)->assertRedirect();

        $unauthorized = User::create(['name' => 'No Prompt', 'email' => 'no-prompt@example.test', 'password' => bcrypt('password'), 'permissions' => []]);
        $this->actingAs($unauthorized)->post($route)->assertForbidden();

        $authorized = User::create(['name' => 'Prompt User', 'email' => 'prompt@example.test', 'password' => bcrypt('password'), 'permissions' => ['platform.index' => true]]);
        $response = $this->actingAs($authorized)->post($route)->assertOk();
        $response->assertHeader('content-type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString(implode(',', ProductCsvContract::HEADERS), $response->getContent());
        $this->assertStringNotContainsString('{{CSV_HEADERS}}', $response->getContent());
    }
}
