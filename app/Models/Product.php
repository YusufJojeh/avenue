<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Services\ImageService;
use App\Traits\Translatable;

class Product extends Model
{
    use AsSource, Filterable, Translatable;

    protected $fillable = [
        'category_id','brand_id','name','slug','short_description','description',
        'name_en','name_ar','slug_en','slug_ar','short_description_en','short_description_ar','description_en','description_ar',
        'seo_title_ar','seo_title_en','seo_description_ar','seo_description_en',
        'sku','price','sale_price','stock_qty','is_active','is_featured','published_at',
    ];

    // Type casting for interface
    protected $casts = [
        'is_active'     => 'bool',
        'is_featured'   => 'bool',
        'published_at'  => 'datetime',
        'price'         => 'decimal:2',
        'sale_price'    => 'decimal:2',
    ];

    // Computed attributes returned automatically with the model
    protected $appends = ['primary_image_url', 'effective_price'];

    // For sorting/filtering in Orchid tables
    protected $allowedSorts   = ['name','price','sale_price','stock_qty','is_active','is_featured','published_at','created_at'];
    protected $allowedFilters = ['name','is_active','is_featured'];

    // Relationships
    public function category()     { return $this->belongsTo(Category::class); }
    public function brand()        { return $this->belongsTo(Brand::class); }

    // Order images by default (first sort_order then id)
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_product');
    }

    // Order sizes by sort_order
    public function sizes()
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order')->orderBy('size');
    }

    public function activeSizes()
    {
        return $this->hasMany(ProductSize::class)->where('is_active', true)->orderBy('sort_order')->orderBy('size');
    }

    // Scopes
    public function scopeActive($q)        { return $q->where('is_active', true); }
    public function scopeFeatured($q)      { return $q->where('is_featured', true); }
    public function scopeExternalBrand($q) { return $q->whereHas('brand', fn($b)=>$b->where('is_external', 1)); }
    
    public function scopePublished($q) {
        return $q->where(function($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', now());
        });
    }

    /**
     * Get the primary image URL for the product
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $imageService = app(ImageService::class);

        if ($this->relationLoaded('images')) {
            $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

            return $primary?->path
                ? $imageService->getUrl($primary->path, 'images/placeholder-product.png')
                : asset('images/placeholder-product.png');
        }

        // Try primary image first
        $primary = $this->primaryImage()->first();
        if ($primary && $primary->path) {
            return $imageService->getUrl($primary->path, 'images/placeholder-product.png');
        }

        // Fallback to first image
        $first = $this->images()->orderBy('sort_order')->first();
        if ($first && $first->path) {
            return $imageService->getUrl($first->path, 'images/placeholder-product.png');
        }

        // Return fallback
        return asset('images/placeholder-product.png');
    }

    /**
     * Effective price (sale_price if valid and less than base price)
     */
    protected function effectivePrice(): Attribute
    {
        return Attribute::get(function () {
            $price = (float) $this->price;
            $sale  = is_null($this->sale_price) ? null : (float) $this->sale_price;

            return ($sale !== null && $sale >= 0 && $sale < $price) ? $sale : $price;
        });
    }

    /**
     * Get localized name attribute
     */
    public function getNameAttribute($value)
    {
        return $this->getLocalizedAttribute('name') ?? $value;
    }

    /**
     * Get localized description attribute
     */
    public function getDescriptionAttribute($value)
    {
        return $this->getLocalizedAttribute('description') ?? $value;
    }

    /**
     * Get localized short description attribute
     */
    public function getShortDescriptionAttribute($value)
    {
        return $this->getLocalizedAttribute('short_description') ?? $value;
    }

    /**
     * Get localized slug attribute
     */
    public function getSlugAttribute($value)
    {
        return $this->getLocalizedAttribute('slug') ?? $value;
    }

    /**
     * Delete images and files when deleting product (additional protection alongside admin screen)
     */
    protected static function booted()
    {
        static::deleting(function (Product $product) {
            foreach ($product->images as $img) {
                $img->deleteImageFile();
            }
            $product->images()->delete();
        });
    }
}
