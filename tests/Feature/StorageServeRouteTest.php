<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regression coverage for the production host not reliably following the
 * public_html/storage symlink for static files: the "public" disk's
 * `serve => true` config (config/filesystems.php) makes Laravel's built-in
 * local-disk file server handle `/storage/{path}` at the application level
 * instead, so image URLs keep working even when the symlink isn't followed.
 */
class StorageServeRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_serves_a_file_from_the_public_disk_with_the_full_nested_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/file-abc123.png', 'fake-png-bytes');

        $response = $this->get('/storage/categories/file-abc123.png');

        $response->assertOk();
        $this->assertSame('fake-png-bytes', $response->streamedContent());
    }

    public function test_it_returns_404_for_a_missing_file(): void
    {
        Storage::fake('public');

        $response = $this->get('/storage/categories/does-not-exist.png');

        $response->assertNotFound();
    }

    public function test_it_rejects_path_traversal_attempts(): void
    {
        Storage::fake('public');

        $response = $this->get('/storage/categories/../../outside.txt');

        $response->assertNotFound();
    }
}
