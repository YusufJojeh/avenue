<?php

namespace App\Services;

class ProductCsvContract
{
    public const LIST_SEPARATOR = '|';

    public const HEADERS = [
        'name_ar', 'name_en', 'slug_ar', 'slug_en', 'category', 'brand', 'sku',
        'price', 'sale_price', 'stock_qty', 'sizes', 'short_description_ar',
        'short_description_en', 'description_ar', 'description_en', 'is_active',
        'is_featured', 'seo_title_ar', 'seo_title_en', 'seo_description_ar',
        'seo_description_en', 'primary_image_url', 'image_urls',
    ];

    public const REQUIRED_HEADERS = ['name_ar', 'name_en', 'category', 'price'];

    public const OPTIONAL_HEADERS = [
        'slug_ar', 'slug_en', 'brand', 'sku', 'sale_price', 'stock_qty', 'sizes',
        'short_description_ar', 'short_description_en', 'description_ar',
        'description_en', 'is_active', 'is_featured', 'seo_title_ar',
        'seo_title_en', 'seo_description_ar', 'seo_description_en',
        'primary_image_url', 'image_urls',
    ];

    public static function template(): string
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, self::HEADERS, escape: '');
        fputcsv($stream, self::exampleRow(), escape: '');
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv;
    }

    public static function chatGptPrompt(): string
    {
        $template = file_get_contents(resource_path('templates/product-links-to-csv-prompt.txt'));

        return str_replace('{{CSV_HEADERS}}', implode(',', self::HEADERS), $template);
    }

    private static function exampleRow(): array
    {
        return [
            'منتج تجريبي', 'Example Product', '', '', 'replace-with-existing-category', '',
            'EXAMPLE-001', '99.00', '79.00', '10', 'S|M|L', 'وصف قصير',
            'Short description', 'وصف المنتج التجريبي', 'Example product description', 'yes',
            'no', 'عنوان تجريبي', 'Example SEO title', 'وصف ميتا تجريبي',
            'Example meta description', 'https://example.com/product-main.jpg',
            'https://example.com/product-side.jpg|https://example.com/product-back.jpg',
        ];
    }
}
