<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class ProductSize extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'sale_price',
        'stock_qty',
        'sku',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // For Orchid tables
    protected $allowedSorts = ['size', 'sort_order', 'stock_qty', 'is_active', 'created_at'];
    protected $allowedFilters = ['size', 'is_active'];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get effective price (sale_price if valid and less than base price, otherwise price or product price)
     */
    public function getEffectivePriceAttribute(): float
    {
        // If this size has its own price
        if ($this->price !== null) {
            $price = (float) $this->price;
            $sale = $this->sale_price !== null ? (float) $this->sale_price : null;
            return ($sale !== null && $sale >= 0 && $sale < $price) ? $sale : $price;
        }

        // Fallback to product price
        return (float) $this->product->effective_price;
    }

    /**
     * Check if size is in stock
     */
    public function isInStock(): bool
    {
        // If stock_qty is 0, consider it as unlimited (or check product stock)
        if ($this->stock_qty === 0 && $this->product->stock_qty > 0) {
            return true;
        }
        return $this->stock_qty > 0;
    }
}
