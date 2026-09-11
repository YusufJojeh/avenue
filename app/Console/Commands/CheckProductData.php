<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class CheckProductData extends Command
{
    protected $signature = 'products:check-data';
    protected $description = 'Check product data for display issues';

    public function handle()
    {
        $this->info('🔍 فحص بيانات المنتجات...');

        $issues = [];
        $products = Product::with(['category', 'brand'])->get();

        foreach ($products as $product) {
            $productIssues = [];

            // 1. فحص الاسم المكرر
            $duplicateNames = Product::where('name', $product->name)
                ->where('id', '!=', $product->id)
                ->count();

            if ($duplicateNames > 0) {
                $productIssues[] = "اسم مكرر: '{$product->name}'";
            }

            // 2. فحص المخزون
            if ($product->stock_qty <= 0) {
                $productIssues[] = "نفد المخزون: {$product->stock_qty}";
            }

            // 3. فحص السعر
            if ($product->price <= 0) {
                $productIssues[] = "سعر غير صحيح: {$product->price}";
            }

            // 4. فحص العلاقات
            if (!$product->category) {
                $productIssues[] = "فئة غير موجودة: {$product->category_id}";
            }

            if (!$product->brand) {
                $productIssues[] = "علامة تجارية غير موجودة: {$product->brand_id}";
            }

            // 5. فحص الحقول المطلوبة
            if (empty($product->name)) {
                $productIssues[] = "اسم المنتج فارغ";
            }

            if (empty($product->slug)) {
                $productIssues[] = "رابط المنتج فارغ";
            }

            if ($productIssues) {
                $issues[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'issues' => $productIssues
                ];
            }
        }

        // عرض النتائج
        if (empty($issues)) {
            $this->info('✅ جميع المنتجات سليمة!');
        } else {
            $this->warn('⚠️ تم العثور على مشاكل:');

            foreach ($issues as $issue) {
                $this->error("المنتج #{$issue['id']}: {$issue['name']}");
                foreach ($issue['issues'] as $problem) {
                    $this->line("  - {$problem}");
                }
                $this->line('');
            }
        }

        // إحصائيات
        $this->info('📊 إحصائيات:');
        $this->line("إجمالي المنتجات: {$products->count()}");
        $this->line("منتجات بالمخزون: " . $products->where('stock_qty', '>', 0)->count());
        $this->line("منتجات نفد مخزونها: " . $products->where('stock_qty', '<=', 0)->count());
        $this->line("منتجات نشطة: " . $products->where('is_active', 1)->count());
        $this->line("منتجات مميزة: " . $products->where('is_featured', 1)->count());

        return Command::SUCCESS;
    }
}
