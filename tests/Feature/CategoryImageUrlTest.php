<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryImageUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_url_keeps_the_full_nested_path_no_basename_truncation(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'categories/file-00000000abc123_16-20-17_xoRsNOIH.png',
            'fake-image-bytes'
        );

        $category = Category::create([
            'name' => 'Widgets',
            'slug' => 'widgets',
        ]);
        $category->image_path = 'categories/file-00000000abc123_16-20-17_xoRsNOIH.png';
        $category->save();

        $url = $category->image_url;

        $this->assertStringContainsString('/storage/categories/file-00000000abc123_16-20-17_xoRsNOIH.png', $url);
        $this->assertStringNotContainsString('/storage/file-00000000abc123', $url);
    }

    public function test_image_url_falls_back_gracefully_when_there_is_no_image(): void
    {
        $category = Category::create([
            'name' => 'No Image',
            'slug' => 'no-image',
        ]);

        $url = $category->image_url;

        $this->assertNotNull($url);
        $this->assertStringContainsString('placeholder-category.png', $url);
    }

    public function test_image_url_falls_back_when_the_stored_path_does_not_exist_on_disk(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name' => 'Missing File',
            'slug' => 'missing-file',
        ]);
        $category->image_path = 'categories/does-not-exist.png';
        $category->save();

        $url = $category->image_url;

        $this->assertStringContainsString('placeholder-category.png', $url);
    }

    public function test_updating_a_category_without_a_new_upload_retains_the_previous_image_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/original.png', 'bytes');

        $category = Category::create([
            'name' => 'Keeps Image',
            'slug' => 'keeps-image',
        ]);
        $category->image_path = 'categories/original.png';
        $category->save();

        // Simulate an edit that doesn't touch the image field, mirroring
        // CategoryEditScreen::createOrUpdate() when no file is uploaded.
        $category->fill(['name' => 'Keeps Image Renamed']);
        $category->save();

        $this->assertSame('categories/original.png', $category->fresh()->image_path);
    }
}
