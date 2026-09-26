<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\EnhancedPerformanceService;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function __construct(
        private EnhancedPerformanceService $performance
    ) {}

    public function created(Product $product): void
    {
        $this->invalidateCache();
    }

    public function updated(Product $product): void
    {
        $this->invalidateCache();
    }

    public function deleted(Product $product): void
    {
        $this->invalidateCache();
    }

    private function invalidateCache(): void
    {
        $this->performance->clearCache('products');
        Cache::forget('platform.dashboard.stats');
        Cache::forget('layout.nav_categories');
    }
}



