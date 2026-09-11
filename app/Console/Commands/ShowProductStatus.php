<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class ShowProductStatus extends Command
{
    protected $signature = 'products:status';
    protected $description = 'Show product display status';

    public function handle()
    {
        $this->info('📊 حالة عرض المنتجات');
        $this->line('');

        $products = Product::with(['category', 'brand'])->get();

        $this->table(
            ['ID', 'الاسم', 'السعر', 'المخزون', 'الحالة', 'المشاكل'],
            $products->map(function ($product) {
                $issues = [];

                // فحص المخزون
                if ($product->stock_qty <= 0) {
                    $issues[] = 'نفد المخزون';
                }

                // فحص السعر
                if ($product->price <= 0) {
                    $issues[] = 'سعر غير صحيح';
                }

                // فحص العلاقات
                if (!$product->category) {
                    $issues[] = 'فئة مفقودة';
                }

                if (!$product->brand) {
                    $issues[] = 'علامة تجارية مفقودة';
                }

                // فحص الحقول المطلوبة
                if (empty($product->name)) {
                    $issues[] = 'اسم فارغ';
                }

                if (empty($product->slug)) {
                    $issues[] = 'رابط فارغ';
                }

                $status = $product->is_active ? 'نشط' : 'غير نشط';
                if ($product->stock_qty <= 0) {
                    $status .= ' (نفد المخزون)';
                }

                return [
                    $product->id,
                    Str::limit($product->name, 30),
                    $product->price,
                    $product->stock_qty,
                    $status,
                    implode(', ', $issues) ?: 'سليم'
                ];
            })
        );

        // إحصائيات
        $this->line('');
        $this->info('📈 إحصائيات:');
        $this->line("إجمالي المنتجات: {$products->count()}");
        $this->line("منتجات نشطة: " . $products->where('is_active', 1)->count());
        $this->line("منتجات بالمخزون: " . $products->where('stock_qty', '>', 0)->count());
        $this->line("منتجات نفد مخزونها: " . $products->where('stock_qty', '<=', 0)->count());
        $this->line("منتجات مميزة: " . $products->where('is_featured', 1)->count());

        return Command::SUCCESS;
    }
}
