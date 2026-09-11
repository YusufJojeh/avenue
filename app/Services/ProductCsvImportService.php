<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductCsvImportService
{
    public function __construct(private readonly RemoteProductImageService $images) {}

    public function import(array $preview): array
    {
        if (($preview['summary']['invalid'] ?? 0) > 0) {
            throw ValidationException::withMessages(['csv' => 'Import blocked: resolve all invalid rows first.']);
        }

        $createdPaths = [];
        try {
            return DB::transaction(function () use ($preview, &$createdPaths): array {
            $results = [];
            $imported = 0;
            $skipped = 0;
            $warnings = 0;
            $imagesImported = 0;
            $imageWarnings = 0;

            foreach ($preview['rows'] as $row) {
                $data = $row['normalized'];

                if ($data['duplicate_in_csv']) {
                    $skipped++;
                    $warnings++;
                    $results[] = $this->result($row, 'SKIPPED_DUPLICATE', 'Duplicate product inside CSV.');
                    continue;
                }

                $existing = Product::query()->where('slug', $data['slug']);
                if (($data['sku'] ?? '') !== '') {
                    $existing->orWhere('sku', $data['sku']);
                }
                if ($existing->exists()) {
                    $skipped++;
                    $warnings++;
                    $results[] = $this->result($row, 'SKIPPED_EXISTING', 'Existing SKU or slug; no changes made.');
                    continue;
                }

                $brandWarning = ($data['brand'] ?? '') !== '' && $data['brand_id'] === null;
                $product = Product::create($this->productAttributes($data));

                foreach ($this->uniqueSizes($data['sizes']) as $index => $size) {
                    $product->sizes()->create([
                        'size' => $size,
                        'price' => null,
                        'sale_price' => null,
                        'stock_qty' => 0,
                        'sku' => null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }

                $seenHashes = [];
                $rowImageWarnings = [];
                $rowImages = 0;
                foreach ($data['image_urls'] as $url) {
                    $image = $this->images->ingest($url);
                    if (!$image['success']) {
                        $imageWarnings++;
                        $rowImageWarnings[] = $image['warning'];
                        continue;
                    }
                    if (isset($seenHashes[$image['hash']])) {
                        Storage::disk('public')->delete($image['path']);
                        continue;
                    }
                    $seenHashes[$image['hash']] = true;
                    $createdPaths[] = $image['path'];
                    $product->images()->create(['path' => $image['path'], 'alt' => $product->name, 'is_primary' => $rowImages === 0, 'sort_order' => $rowImages]);
                    $rowImages++;
                    $imagesImported++;
                }

                $imported++;
                if ($brandWarning) $warnings++;
                $reason = ($brandWarning ? 'Unknown brand ignored. ' : '')."Created. {$rowImages} images imported.";
                if ($rowImageWarnings !== []) $reason .= ' '.count($rowImageWarnings).' skipped: '.implode(', ', $rowImageWarnings).'.';
                $results[] = $this->result($row, 'IMPORTED', $reason);
            }

            return [
                'summary' => ['imported' => $imported, 'skipped' => $skipped, 'images_imported' => $imagesImported, 'image_warnings' => $imageWarnings, 'warnings' => $warnings, 'failed' => 0],
                'rows' => $results,
            ];
            });
        } catch (\Throwable $e) {
            if ($createdPaths !== []) Storage::disk('public')->delete($createdPaths);
            throw $e;
        }
    }

    private function productAttributes(array $data): array
    {
        $name = $data['name_en'] !== '' ? $data['name_en'] : $data['name_ar'];

        return [
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'name' => $name,
            'slug' => $data['slug'],
            'short_description' => $data['short_description_en'] ?? $data['short_description_ar'] ?? null,
            'description' => $data['description_en'] ?? $data['description_ar'] ?? null,
            'name_en' => $data['name_en'] ?: null,
            'name_ar' => $data['name_ar'] ?: null,
            'slug_en' => ($data['slug_en'] ?? '') !== '' ? $data['slug_en'] : null,
            'slug_ar' => ($data['slug_ar'] ?? '') !== '' ? $data['slug_ar'] : null,
            'short_description_en' => ($data['short_description_en'] ?? '') ?: null,
            'short_description_ar' => ($data['short_description_ar'] ?? '') ?: null,
            'description_en' => ($data['description_en'] ?? '') ?: null,
            'description_ar' => ($data['description_ar'] ?? '') ?: null,
            'seo_title_ar' => ($data['seo_title_ar'] ?? '') ?: null,
            'seo_title_en' => ($data['seo_title_en'] ?? '') ?: null,
            'seo_description_ar' => ($data['seo_description_ar'] ?? '') ?: null,
            'seo_description_en' => ($data['seo_description_en'] ?? '') ?: null,
            'sku' => ($data['sku'] ?? '') !== '' ? $data['sku'] : 'SKU-'.strtoupper(Str::random(8)),
            'price' => $data['price'],
            'sale_price' => $data['sale_price'],
            'stock_qty' => $data['stock_qty'] ?? 0,
            'is_active' => $data['is_active'],
            'is_featured' => $data['is_featured'],
            'published_at' => now(),
        ];
    }

    private function uniqueSizes(array $sizes): array
    {
        $seen = [];
        return array_values(array_filter($sizes, function (string $size) use (&$seen): bool {
            $key = mb_strtolower(trim($size), 'UTF-8');
            if ($key === '' || isset($seen[$key])) return false;
            return $seen[$key] = true;
        }));
    }

    private function result(array $row, string $result, string $reason): array
    {
        return ['row' => $row['row'], 'name' => $row['name'], 'result' => $result, 'reason' => $reason];
    }
}
