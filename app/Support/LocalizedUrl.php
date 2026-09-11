<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocalizedUrl
{
    public static function alternates(?object $entity = null): array
    {
        $route = Route::currentRouteName();
        if (!$route || !in_array($route, self::publicRoutes(), true)) return [];
        $params = request()->route()?->parameters() ?? [];
        $urls = [];

        foreach (['ar', 'en'] as $locale) {
            $localized = $params;
            $localized['locale'] = $locale;
            if ($entity && array_key_exists('slug', $localized)) {
                $localized['slug'] = self::slug($entity, $locale);
            }
            $urls[$locale] = route($route, $localized);
        }
        $urls['x-default'] = $urls[config('app.default_locale', 'en')] ?? $urls['en'];

        return $urls;
    }

    public static function switchTo(string $locale, array $alternates = []): string
    {
        return $alternates[$locale] ?? self::alternates()[$locale] ?? route('home', ['locale' => $locale]);
    }

    public static function slug(object $entity, string $locale): string
    {
        $localized = self::raw($entity, 'slug_'.$locale);
        $english = self::raw($entity, 'slug_en');
        $base = self::raw($entity, 'base_slug') ?: self::raw($entity, 'slug');
        return (string) ($localized ?: $english ?: $base);
    }

    private static function raw(object $entity, string $field): mixed
    {
        if (method_exists($entity, 'getRawOriginal')) return $entity->getRawOriginal($field);
        return $entity->{$field} ?? null;
    }

    private static function publicRoutes(): array
    {
        return ['home', 'products.index', 'products.show', 'categories.index', 'categories.show', 'brands.index', 'brands.show', 'search', 'wishlist.index', 'about', 'contact', 'faq', 'privacy-policy', 'terms-of-service'];
    }
}
