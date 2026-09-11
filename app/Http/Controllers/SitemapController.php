<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Response;
use App\Support\LocalizedUrl;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products = Product::active()->published()
            ->select('slug', 'slug_ar', 'slug_en', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = Category::active()
            ->select('slug', 'slug_ar', 'slug_en', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $brands = Brand::active()->select('slug', 'slug_ar', 'slug_en', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach (['ar', 'en'] as $locale) {
            $xml .= $this->url(route('home', ['locale' => $locale]), null, 'daily', '1.0');
            foreach (['products.index', 'categories.index', 'brands.index'] as $routeName) {
                $xml .= $this->url(route($routeName, ['locale' => $locale]), null, 'daily', '0.8');
            }
            foreach (['about', 'contact', 'faq'] as $routeName) {
                $xml .= $this->url(route($routeName, ['locale' => $locale]), null, 'monthly', '0.3');
            }
        }

        // Products
        foreach ($products as $product) {
            foreach (['ar', 'en'] as $locale) {
                $xml .= $this->url(route('products.show', ['locale' => $locale, 'slug' => LocalizedUrl::slug($product, $locale)]), $product->updated_at?->toW3cString(), 'weekly', '0.9');
            }
        }

        // Categories
        foreach ($categories as $category) {
            foreach (['ar', 'en'] as $locale) {
                $xml .= $this->url(route('categories.show', ['locale' => $locale, 'slug' => LocalizedUrl::slug($category, $locale)]), $category->updated_at?->toW3cString(), 'weekly', '0.7');
            }
        }

        // Brands
        foreach ($brands as $brand) {
            foreach (['ar', 'en'] as $locale) {
                $xml .= $this->url(route('brands.show', ['locale' => $locale, 'slug' => LocalizedUrl::slug($brand, $locale)]), $brand->updated_at?->toW3cString(), 'weekly', '0.6');
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function url(string $loc, ?string $lastmod, string $changefreq, string $priority): string
    {
        $entry = '<url>';
        $entry .= '<loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
        if ($lastmod) {
            $entry .= '<lastmod>' . $lastmod . '</lastmod>';
        }
        $entry .= '<changefreq>' . $changefreq . '</changefreq>';
        $entry .= '<priority>' . $priority . '</priority>';
        $entry .= '</url>';

        return $entry;
    }
}
