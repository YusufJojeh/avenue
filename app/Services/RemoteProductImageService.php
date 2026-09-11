<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Throwable;

class RemoteProductImageService
{
    public const MAX_IMAGES_PER_PRODUCT = 10;
    public const MAX_BYTES = 8 * 1024 * 1024;
    public const MAX_REDIRECTS = 2;
    public const REQUEST_TIMEOUT = 8;
    public const CONNECT_TIMEOUT = 3;
    public const MAX_DIMENSION = 10000;
    public const MAX_PIXELS = 40000000;
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public function ingest(string $url): array
    {
        $storedPath = null;
        try {
            $response = $this->fetch($url);
            if (isset($response['warning'])) return $response;
            $bytes = $this->readBounded($response['response']);
            if (is_array($bytes)) return $bytes;
            $info = @getimagesizefromstring($bytes);
            if ($info === false || ($info[0] ?? 0) < 1 || ($info[1] ?? 0) < 1) return $this->failure('IMAGE_INVALID_CONTENT', 'Invalid image content.');
            if (!in_array($info['mime'] ?? '', self::ALLOWED_MIMES, true)) return $this->failure('IMAGE_UNSUPPORTED_FORMAT', 'Unsupported image format.');
            if ($info[0] > self::MAX_DIMENSION || $info[1] > self::MAX_DIMENSION || $info[0] * $info[1] > self::MAX_PIXELS) return $this->failure('IMAGE_INVALID_CONTENT', 'Image dimensions are too large.');
            $hash = hash('sha256', $bytes);
            $image = Image::read($bytes)->orient()->scaleDown(width: 1200, height: 1200);
            $path = 'products/'.Str::uuid().'.webp';
            if (!Storage::disk('public')->put($path, (string) $image->toWebp(quality: 85))) return $this->failure('IMAGE_PROCESSING_FAILED', 'Image could not be stored.');
            $storedPath = $path;
            return ['success' => true, 'path' => $path, 'hash' => $hash, 'width' => $image->width(), 'height' => $image->height(), 'mime' => 'image/webp', 'warning' => null];
        } catch (Throwable $e) {
            if ($storedPath !== null) Storage::disk('public')->delete($storedPath);
            Log::warning('Remote product image failed', ['exception' => $e::class]);
            return $this->failure('IMAGE_DOWNLOAD_FAILED', 'Image download failed.');
        }
    }

    private function fetch(string $url): array
    {
        for ($redirects = 0; $redirects <= self::MAX_REDIRECTS; $redirects++) {
            $target = $this->validatedTarget($url);
            if (isset($target['warning'])) return $redirects > 0 ? $this->failure('IMAGE_REDIRECT_BLOCKED', 'Image redirect was blocked.') : $target;
            $options = ['allow_redirects' => false, 'stream' => true];
            if (defined('CURLOPT_RESOLVE') && $target['ip'] !== null) {
                $port = parse_url($url, PHP_URL_PORT) ?: (parse_url($url, PHP_URL_SCHEME) === 'https' ? 443 : 80);
                $options['curl'] = [CURLOPT_RESOLVE => ["{$target['host']}:{$port}:{$target['ip']}"]];
            }
            $response = Http::withOptions($options)->connectTimeout(self::CONNECT_TIMEOUT)->timeout(self::REQUEST_TIMEOUT)->get($url);
            if ($response->redirect()) {
                if ($redirects === self::MAX_REDIRECTS) return $this->failure('IMAGE_REDIRECT_BLOCKED', 'Too many image redirects.');
                $location = $response->header('Location');
                if (!$location) return $this->failure('IMAGE_DOWNLOAD_FAILED', 'Image redirect was invalid.');
                $url = $this->redirectUrl($url, $location);
                continue;
            }
            if (!$response->successful()) return $this->failure('IMAGE_DOWNLOAD_FAILED', 'Image download failed.');
            $length = $response->header('Content-Length');
            if ($length !== null && ctype_digit(trim($length)) && (int) $length > self::MAX_BYTES) return $this->failure('IMAGE_TOO_LARGE', 'Image exceeds the size limit.');
            return ['response' => $response];
        }
        return $this->failure('IMAGE_REDIRECT_BLOCKED', 'Too many image redirects.');
    }

    private function validatedTarget(string $url): array
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) return $this->failure('IMAGE_INVALID_URL', 'Invalid image URL.');
        $parts = parse_url($url);
        if (!in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) return $this->failure('IMAGE_INVALID_URL', 'Invalid image URL.');
        $host = trim($parts['host'], '[]');
        if (strcasecmp($host, 'localhost') === 0) return $this->failure('IMAGE_BLOCKED_HOST', 'Image host is not allowed.');
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : $this->resolve($host);
        if ($ips === [] || count(array_filter($ips, fn (string $ip) => !$this->isPublicIp($ip))) > 0) return $this->failure('IMAGE_BLOCKED_HOST', 'Image host is not allowed.');
        return ['host' => $host, 'ip' => $ips[0] ?? null];
    }

    private function resolve(string $host): array
    {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA) ?: [];
        return array_values(array_unique(array_filter(array_map(fn (array $record) => $record['ip'] ?? $record['ipv6'] ?? null, $records))));
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function readBounded(Response $response): string|array
    {
        $stream = $response->toPsrResponse()->getBody();
        $bytes = '';
        while (!$stream->eof()) {
            $bytes .= $stream->read(8192);
            if (strlen($bytes) > self::MAX_BYTES) return $this->failure('IMAGE_TOO_LARGE', 'Image exceeds the size limit.');
        }
        return $bytes === '' ? $this->failure('IMAGE_INVALID_CONTENT', 'Invalid image content.') : $bytes;
    }

    private function redirectUrl(string $base, string $location): string
    {
        if (preg_match('#^https?://#i', $location)) return $location;
        $parts = parse_url($base);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
        if (isset($parts['port'])) $origin .= ':'.$parts['port'];
        if (str_starts_with($location, '/')) return $origin.$location;
        return $origin.(preg_replace('#/[^/]*$#', '/', $parts['path'] ?? '/')).$location;
    }

    private function failure(string $code, string $message): array
    {
        return ['success' => false, 'warning' => $code, 'message' => $message];
    }
}
