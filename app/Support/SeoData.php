<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeoData
{
    public static function product(object $product): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $title = trim((string) ($product->{'seo_title_'.$locale} ?? '')) ?: (string) $product->name;
        $description = trim((string) ($product->{'seo_description_'.$locale} ?? ''))
            ?: trim(strip_tags((string) ($product->short_description ?: $product->description ?: $product->name)));
        $canonical = route('products.show', ['slug' => LocalizedUrl::slug($product, app()->getLocale())]);

        return self::meta($title, Str::limit($description, 160, ''), $canonical, $product->primary_image_url, 'product', LocalizedUrl::alternates($product));
    }

    public static function category(object $category): array
    {
        $site = config('app.name', 'AVENUE');
        $description = trim(strip_tags((string) ($category->description ?? '')))
            ?: "Browse {$category->name} products at {$site}.";

        $canonical = route('categories.show', ['slug' => LocalizedUrl::slug($category, app()->getLocale())]);
        return self::meta("{$category->name} - {$site}", Str::limit($description, 160, ''), self::paginated($canonical), $category->image_url ?? null, 'website', LocalizedUrl::alternates($category));
    }

    public static function brand(object $brand): array
    {
        $site = config('app.name', 'AVENUE');
        $canonical = route('brands.show', ['slug' => LocalizedUrl::slug($brand, app()->getLocale())]);
        return self::meta("{$brand->name} - {$site}", "Browse {$brand->name} products at {$site}.", self::paginated($canonical), $brand->logo_url ?? null, 'website', LocalizedUrl::alternates($brand));
    }

    public static function listing(string $title, string $description, string $canonical): array
    {
        return self::meta($title, $description, $canonical, null, 'website', LocalizedUrl::alternates());
    }

    public static function productSchemas(object $product): array
    {
        $url = route('products.show', ['slug' => LocalizedUrl::slug($product, app()->getLocale())]);
        $schema = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $product->name, 'url' => $url];
        if ($product->short_description || $product->description) $schema['description'] = strip_tags($product->short_description ?: $product->description);
        if ($product->primary_image_url) $schema['image'] = $product->primary_image_url;
        if ($product->sku) $schema['sku'] = $product->sku;
        if ($product->brand) $schema['brand'] = ['@type' => 'Brand', 'name' => $product->brand->name];
        if ($product->category) $schema['category'] = $product->category->name;
        $schema['offers'] = ['@type' => 'Offer', 'url' => $url, 'priceCurrency' => config('app.currency', 'USD'), 'price' => $product->effective_price, 'availability' => $product->stock_qty > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'];

        $crumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
        ];
        if ($product->category) $crumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('categories.show', ['slug' => LocalizedUrl::slug($product->category, app()->getLocale())])];
        $crumbs[] = ['@type' => 'ListItem', 'position' => count($crumbs) + 1, 'name' => $product->name];

        return [$schema, ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbs]];
    }

    private static function meta(string $title, string $description, string $canonical, ?string $image, string $type = 'website', array $alternates = []): array
    {
        return compact('title', 'description', 'canonical', 'image', 'type', 'alternates') + ['robots' => 'index, follow'];
    }

    private static function paginated(string $url): string
    {
        $page = max(1, (int) request()->query('page', 1));
        return $page > 1 ? $url.'?page='.$page : $url;
    }
}
