<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Traits\HasImages;
use App\Traits\Translatable;

class Brand extends Model
{
    use AsSource, Filterable, HasImages, Translatable;

    protected $fillable = [
        'name', 'slug', 'is_external', 'is_active', 'logo_path', 'sort_order',
        'name_en', 'name_ar', 'slug_en', 'slug_ar',
    ];

    protected $allowedSorts   = ['name', 'is_external', 'is_active', 'sort_order', 'created_at'];
    protected $allowedFilters = ['name', 'is_external', 'is_active'];

    // إرجاع logo_url تلقائياً
    protected $appends = ['logo_url'];

    // (اختياري) تحويلات أنظف
    protected $casts = [
        'is_external' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function scopeActive($q){ return $q->where('is_active', true); }
    public function scopeExternal($q){ return $q->where('is_external', true); }

    /**
     * Get the logo URL attribute
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return asset('images/placeholder-brand.png');
        }

        // If it's a full URL, return as is
        if (preg_match('#^https?://#i', $this->logo_path)) {
            return $this->logo_path;
        }

        $imageService = app(\App\Services\ImageService::class);
        return $imageService->getUrl($this->logo_path, 'images/placeholder-brand.png');
    }

    /**
     * تهيئة إعدادات الصور الخاصة بالـ Brand (تستخدمها HasImages)
     */
    protected function getImagePathField(): string
    {
        return 'logo_path';
    }

    protected function getImageDirectory(): string
    {
        return 'brands';
    }

    protected function getImageFallback(): ?string
    {
        return 'images/placeholder-brand.png';
    }

    /**
     * Get the products for this brand
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get localized name attribute
     */
    public function getNameAttribute($value)
    {
        return $this->getLocalizedAttribute('name') ?? $value;
    }

    /**
     * Get localized slug attribute
     */
    public function getSlugAttribute($value)
    {
        return $this->getLocalizedAttribute('slug') ?? $value;
    }
}
