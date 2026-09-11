<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductCsvPreviewService
{
    public const MAX_ROWS = 500;
    public const SEO_TITLE_MAX = 255;
    public const SEO_DESCRIPTION_MAX = 500;

    public function preview(UploadedFile|string $file): array
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages(['csv' => 'The CSV could not be read.']);
        }

        try {
            $headers = fgetcsv($handle, escape: '');
            if ($headers === false) {
                throw ValidationException::withMessages(['csv' => 'The CSV is empty.']);
            }

            $headers = array_map(fn ($header) => strtolower(trim($this->stripBom((string) $header))), $headers);
            $this->validateHeaders($headers);

            $rows = [];
            $identities = [];
            $slugs = [];
            $categoryLookup = $this->lookup(Category::query(), ['name', 'name_ar', 'name_en', 'slug', 'slug_ar', 'slug_en']);
            $brandLookup = $this->lookup(Brand::query(), ['name', 'name_ar', 'name_en', 'slug', 'slug_ar', 'slug_en']);

            while (($values = fgetcsv($handle, escape: '')) !== false) {
                if ($this->isBlankRow($values)) {
                    continue;
                }

                if (count($rows) >= self::MAX_ROWS) {
                    throw ValidationException::withMessages(['csv' => 'The CSV may contain at most '.self::MAX_ROWS.' data rows.']);
                }

                $rowNumber = count($rows) + 2;
                if (count($values) !== count($headers)) {
                    $rows[] = $this->malformedRow($rowNumber);
                    continue;
                }

                $data = array_combine($headers, array_map(fn ($value) => trim((string) $value), $values));
                $rows[] = $this->normalizeRow($data, $rowNumber, $identities, $slugs, $categoryLookup, $brandLookup);
            }
        } finally {
            fclose($handle);
        }

        return [
            'summary' => [
                'total' => count($rows),
                'valid' => count(array_filter($rows, fn ($row) => $row['status'] === 'READY')),
                'warnings' => count(array_filter($rows, fn ($row) => $row['status'] === 'WARNING')),
                'invalid' => count(array_filter($rows, fn ($row) => $row['status'] === 'INVALID')),
            ],
            'rows' => $rows,
        ];
    }

    private function validateHeaders(array $headers): void
    {
        if (count($headers) !== count(array_unique($headers))) {
            throw ValidationException::withMessages(['csv' => 'CSV headers must be unique.']);
        }

        $unknown = array_diff($headers, ProductCsvContract::HEADERS);
        if ($unknown !== []) {
            throw ValidationException::withMessages(['csv' => 'Unsupported CSV columns: '.implode(', ', $unknown)]);
        }

        foreach (ProductCsvContract::REQUIRED_HEADERS as $column) {
            if (!in_array($column, $headers, true)) {
                throw ValidationException::withMessages(['csv' => "Missing required CSV column: {$column}"]);
            }
        }
    }

    private function normalizeRow(array $data, int $rowNumber, array &$identities, array &$slugs, array $categories, array $brands): array
    {
        $errors = [];
        $warnings = [];

        foreach ($data as $column => $value) {
            if ($value !== '' && preg_match('/^[=+@]/', $value)) {
                $errors[] = "{$column} must not contain a spreadsheet formula.";
            }
        }

        if (($data['name_ar'] ?? '') === '' && ($data['name_en'] ?? '') === '') {
            $errors[] = 'At least one product name is required.';
        }

        foreach (['seo_title_ar', 'seo_title_en'] as $field) {
            if (mb_strlen($data[$field] ?? '', 'UTF-8') > self::SEO_TITLE_MAX) {
                $errors[] = "{$field} may not exceed ".self::SEO_TITLE_MAX.' characters.';
            }
        }
        foreach (['seo_description_ar', 'seo_description_en'] as $field) {
            if (mb_strlen($data[$field] ?? '', 'UTF-8') > self::SEO_DESCRIPTION_MAX) {
                $errors[] = "{$field} may not exceed ".self::SEO_DESCRIPTION_MAX.' characters.';
            }
        }

        $price = $this->price($data['price'] ?? '', 'price', true, $errors);
        $salePrice = $this->price($data['sale_price'] ?? '', 'sale_price', false, $errors);
        if ($price !== null && $salePrice !== null && $salePrice > $price) {
            $errors[] = 'sale_price must not exceed price.';
        }

        $stockQty = $data['stock_qty'] ?? '';
        if ($stockQty !== '' && (!ctype_digit($stockQty) || (int) $stockQty < 0)) {
            $errors[] = 'stock_qty must be a non-negative integer.';
        }

        foreach (['is_active', 'is_featured'] as $field) {
            if (($data[$field] ?? '') !== '' && $this->boolean($data[$field]) === null) {
                $errors[] = "{$field} must be 1/0, true/false, or yes/no.";
            }
        }

        $imageUrls = $this->list($data['image_urls'] ?? '');
        if (($data['primary_image_url'] ?? '') !== '') {
            array_unshift($imageUrls, $data['primary_image_url']);
        }
        $imageUrls = array_values(array_unique($imageUrls));
        if (count($imageUrls) > RemoteProductImageService::MAX_IMAGES_PER_PRODUCT) {
            $warnings[] = 'Only the first '.RemoteProductImageService::MAX_IMAGES_PER_PRODUCT.' images will be imported.';
            $imageUrls = array_slice($imageUrls, 0, RemoteProductImageService::MAX_IMAGES_PER_PRODUCT);
        }
        foreach ($imageUrls as $url) {
            if (!$this->isHttpUrl($url)) {
                $errors[] = "Invalid image URL: {$url}";
            }
        }

        $category = $data['category'] ?? '';
        $categoryId = $category === '' ? null : ($categories[$this->key($category)] ?? null);
        if ($category === '') {
            $errors[] = 'Category is required.';
        } elseif ($categoryId === null) {
            $errors[] = 'Unknown category.';
        }

        $brand = $data['brand'] ?? '';
        $brandId = $brand === '' ? null : ($brands[$this->key($brand)] ?? null);
        if ($brand !== '' && $brandId === null) {
            $warnings[] = 'Unknown brand.';
        }

        $slug = ($data['slug_en'] ?? '') !== '' ? $data['slug_en'] : ($data['slug_ar'] ?? '');
        $slug = $slug !== '' ? Str::slug($slug) : Str::slug($data['name_en'] ?? $data['name_ar'] ?? '');
        if ($slug === '') {
            $errors[] = 'A usable product slug is required.';
        }

        $sku = $data['sku'] ?? '';
        $identity = $sku !== '' ? 'sku:'.$this->key($sku) : 'slug:'.$slug;
        $duplicate = isset($identities[$identity]) || ($slug !== '' && isset($slugs[$slug]));
        if ($duplicate) $warnings[] = 'Duplicate product inside CSV; it will be skipped.';
        $identities[$identity] = true;
        if ($slug !== '') $slugs[$slug] = true;

        return [
            'row' => $rowNumber,
            'name' => $data['name_en'] ?: ($data['name_ar'] ?? ''),
            'category' => $category,
            'brand' => $brand,
            'price' => $price,
            'image_count' => count($imageUrls),
            'status' => $errors !== [] ? 'INVALID' : ($warnings !== [] ? 'WARNING' : 'READY'),
            'messages' => array_merge($errors, $warnings),
            'normalized' => array_merge($data, [
                'price' => $price,
                'sale_price' => $salePrice,
                'stock_qty' => $stockQty === '' ? null : (int) $stockQty,
                'sizes' => $this->list($data['sizes'] ?? ''),
                'is_active' => $this->boolean($data['is_active'] ?? '') ?? true,
                'is_featured' => $this->boolean($data['is_featured'] ?? '') ?? false,
                'image_urls' => $imageUrls,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'slug' => $slug,
                'identity' => $identity,
                'duplicate_in_csv' => $duplicate,
            ]),
        ];
    }

    private function malformedRow(int $rowNumber): array
    {
        return ['row' => $rowNumber, 'name' => '', 'category' => '', 'brand' => '', 'price' => null,
            'image_count' => 0, 'status' => 'INVALID', 'messages' => ['Malformed CSV structure.'], 'normalized' => []];
    }

    private function lookup($query, array $columns): array
    {
        return $query->get(array_merge(['id'], $columns))->flatMap(function ($model) use ($columns) {
            return collect($columns)->filter(fn ($column) => filled($model->{$column}))
                ->mapWithKeys(fn ($column) => [$this->key($model->{$column}) => $model->id]);
        })->all();
    }

    private function price(string $value, string $field, bool $required, array &$errors): ?float
    {
        if ($value === '') {
            if ($required) $errors[] = "{$field} is required.";
            return null;
        }
        if (!is_numeric($value) || (float) $value < 0) {
            $errors[] = "{$field} must be numeric and at least 0.";
            return null;
        }
        return round((float) $value, 2);
    }

    private function boolean(string $value): ?bool
    {
        if ($value === '') return null;
        return match (strtolower($value)) {
            '1', 'true', 'yes' => true,
            '0', 'false', 'no' => false,
            default => null,
        };
    }

    private function list(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(ProductCsvContract::LIST_SEPARATOR, $value)), fn ($item) => $item !== ''));
    }

    private function isHttpUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    private function key(string $value): string
    {
        return mb_strtolower(trim($value), 'UTF-8');
    }

    private function stripBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
    }

    private function isBlankRow(array $row): bool
    {
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
    }
}
