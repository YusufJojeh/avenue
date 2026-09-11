<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

class FixProductData extends Command
{
    protected $signature = 'products:fix-data {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix product data issues';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 وضع التجربة - لن يتم إجراء تغييرات');
        }

        $this->info('🛠️ إصلاح بيانات المنتجات...');

        $fixed = 0;
        $products = Product::all();

        foreach ($products as $product) {
            $changes = [];

            // 1. إصلاح الأسماء المكررة
            $duplicateNames = Product::where('name', $product->name)
                ->where('id', '!=', $product->id)
                ->count();

            if ($duplicateNames > 0) {
                $newName = $product->name . ' - ' . $product->id;
                $changes[] = "تغيير الاسم من '{$product->name}' إلى '{$newName}'";

                if (!$dryRun) {
                    $product->name = $newName;
                }
            }

            // 2. إصلاح الروابط المكررة
            $duplicateSlugs = Product::where('slug', $product->slug)
                ->where('id', '!=', $product->id)
                ->count();

            if ($duplicateSlugs > 0) {
                $newSlug = $product->slug . '-' . $product->id;
                $changes[] = "تغيير الرابط من '{$product->slug}' إلى '{$newSlug}'";

                if (!$dryRun) {
                    $product->slug = $newSlug;
                }
            }

            // 3. إصلاح الروابط الفارغة
            if (empty($product->slug)) {
                $newSlug = Str::slug($product->name);
                $changes[] = "إضافة رابط جديد: '{$newSlug}'";

                if (!$dryRun) {
                    $product->slug = $newSlug;
                }
            }

            // 4. إصلاح الأسعار السالبة
            if ($product->price < 0) {
                $changes[] = "إصلاح السعر من {$product->price} إلى 0";

                if (!$dryRun) {
                    $product->price = 0;
                }
            }

            // 5. إصلاح المخزون السالب
            if ($product->stock_qty < 0) {
                $changes[] = "إصلاح المخزون من {$product->stock_qty} إلى 0";

                if (!$dryRun) {
                    $product->stock_qty = 0;
                }
            }

            // 6. إصلاح سعر البيع
            if ($product->sale_price && $product->sale_price >= $product->price) {
                $changes[] = "إزالة سعر البيع غير الصحيح: {$product->sale_price}";

                if (!$dryRun) {
                    $product->sale_price = null;
                }
            }

            if ($changes) {
                $this->warn("المنتج #{$product->id}: {$product->name}");
                foreach ($changes as $change) {
                    $this->line("  - {$change}");
                }

                if (!$dryRun) {
                    $product->save();
                    $fixed++;
                }
            }
        }

        if ($dryRun) {
            $this->info("🔍 سيتم إصلاح {$fixed} منتج");
            $this->warn("قم بتشغيل الأمر بدون --dry-run لتطبيق التغييرات");
        } else {
            $this->info("✅ تم إصلاح {$fixed} منتج");
        }

        return Command::SUCCESS;
    }
}
