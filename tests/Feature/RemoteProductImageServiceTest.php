<?php

namespace Tests\Feature;

use App\Services\RemoteProductImageService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Tests\TestCase;

class RemoteProductImageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_supported_jpeg_png_and_webp_are_verified_and_normalized(): void
    {
        $png = $this->png();
        $formats = [
            'jpg' => (string) Image::read($png)->toJpeg(),
            'png' => $png,
            'webp' => (string) Image::read($png)->toWebp(),
        ];
        foreach ($formats as $extension => $bytes) {
            Http::fake(["http://93.184.216.34/image.{$extension}" => Http::response($bytes, 200)]);
            $result = app(RemoteProductImageService::class)->ingest("http://93.184.216.34/image.{$extension}");
            $this->assertTrue($result['success'], $result['warning'] ?? 'failed');
            $this->assertSame('image/webp', $result['mime']);
            Storage::disk('public')->assertExists($result['path']);
        }
    }

    public function test_private_reserved_and_non_http_destinations_are_blocked(): void
    {
        foreach (['http://localhost/a', 'http://127.0.0.1/a', 'http://10.1.2.3/a', 'http://172.16.1.1/a', 'http://192.168.1.1/a', 'http://169.254.169.254/a', 'http://[::1]/a', 'http://[fc00::1]/a', 'http://[fe80::1]/a', 'file:///tmp/a'] as $url) {
            $result = app(RemoteProductImageService::class)->ingest($url);
            $this->assertFalse($result['success'], $url);
            $this->assertContains($result['warning'], ['IMAGE_BLOCKED_HOST', 'IMAGE_INVALID_URL']);
        }
        Http::assertNothingSent();
    }

    public function test_redirect_to_private_destination_is_blocked(): void
    {
        Http::fake(['http://93.184.216.34/start' => Http::response('', 302, ['Location' => 'http://127.0.0.1/secret'])]);
        $result = app(RemoteProductImageService::class)->ingest('http://93.184.216.34/start');
        $this->assertSame('IMAGE_REDIRECT_BLOCKED', $result['warning']);
        Http::assertSentCount(1);
    }

    public function test_declared_and_actual_oversize_are_rejected(): void
    {
        Http::fake(['http://93.184.216.34/declared' => Http::response('x', 200, ['Content-Length' => (string) (RemoteProductImageService::MAX_BYTES + 1)])]);
        $this->assertSame('IMAGE_TOO_LARGE', app(RemoteProductImageService::class)->ingest('http://93.184.216.34/declared')['warning']);
        Http::fake(['http://93.184.216.34/actual' => Http::response(str_repeat('x', RemoteProductImageService::MAX_BYTES + 1), 200)]);
        $this->assertSame('IMAGE_TOO_LARGE', app(RemoteProductImageService::class)->ingest('http://93.184.216.34/actual')['warning']);
    }

    public function test_fake_html_corrupt_and_unsupported_gif_are_rejected(): void
    {
        $cases = ['html' => '<html></html>', 'broken' => 'broken', 'gif' => base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==')];
        foreach ($cases as $name => $bytes) {
            Http::fake(["http://93.184.216.34/{$name}" => Http::response($bytes, 200, ['Content-Type' => 'image/jpeg'])]);
            $result = app(RemoteProductImageService::class)->ingest("http://93.184.216.34/{$name}");
            $this->assertFalse($result['success']);
        }
    }

    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
    }
}
