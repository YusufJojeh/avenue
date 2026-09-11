<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Traits\HasImages;

class Offer extends Model
{
    use AsSource, Filterable, HasImages;

    protected $fillable = [
        'title','description','type','value','starts_at','ends_at','is_active','banner_image','cta_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'value'     => 'float',
    ];

    // For Orchid tables
    protected $allowedSorts   = ['title','type','value','starts_at','ends_at','is_active','created_at'];
    protected $allowedFilters = ['title','type','is_active'];

    // Computed attributes
    protected $appends = ['banner_url'];

    /**
     * Get the banner URL attribute
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner_image) {
            return asset('images/placeholder-offer.png');
        }

        $imageService = app(\App\Services\ImageService::class);
        return $imageService->getUrl($this->banner_image, 'images/placeholder-offer.png');
    }

    // Table name is "offer_product" by default; adjust if yours is different
    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product');
    }

    // Handy scope if not already present
    public function scopeCurrent($q)
    {
        $now = now();
        return $q->where('is_active', 1)
                 ->where(function ($qq) use ($now) {
                    $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                 })
                 ->where(function ($qq) use ($now) {
                    $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                 });
    }

    /**
     * Override trait methods for offer-specific configuration
     */
    protected function getImagePathField(): string
    {
        return 'banner_image';
    }

    protected function getImageDirectory(): string
    {
        return 'offers';
    }
}
