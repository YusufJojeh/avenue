<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\LocalizedUrl;

class LegacyPublicRedirectController extends Controller
{
    public function product(string $slug)
    {
        $product = Product::active()->published()->where('slug', $slug)->firstOrFail();
        return redirect()->route('products.show', ['locale' => $this->locale(), 'slug' => LocalizedUrl::slug($product, $this->locale())], 301);
    }

    public function category(string $slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();
        return redirect()->route('categories.show', ['locale' => $this->locale(), 'slug' => LocalizedUrl::slug($category, $this->locale())], 301);
    }

    public function brand(string $slug)
    {
        $brand = Brand::active()->where('slug', $slug)->firstOrFail();
        return redirect()->route('brands.show', ['locale' => $this->locale(), 'slug' => LocalizedUrl::slug($brand, $this->locale())], 301);
    }

    private function locale(): string
    {
        return in_array(config('app.default_locale'), ['ar', 'en'], true) ? config('app.default_locale') : 'en';
    }
}
