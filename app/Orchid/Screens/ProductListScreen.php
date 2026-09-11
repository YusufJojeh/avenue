<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen
{
    public function name(): ?string { return 'Products'; }
    public function description(): ?string { return 'Browse and manage products'; }

    public function query(): array
    {
        return [
            'products' => Product::with(['category','brand'])
                ->orderByDesc('created_at')
                ->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create')->icon('bs.plus')->route('platform.products.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('products', [
                TD::make('name')->sort()->filter(TD::FILTER_TEXT),
                TD::make('category.name','Category')->sort(),
                TD::make('brand.name','Brand')->sort(),
                TD::make('price')->render(fn(Product $p) => number_format($p->price, 2)),
                TD::make('sale_price','Sale')->render(fn(Product $p) => $p->sale_price ? number_format($p->sale_price,2) : '—'),
                TD::make('stock_qty','Stock')->sort(),
                TD::make('is_featured','Feat')->render(fn(Product $p)=>$p->is_featured?'Yes':'No')->align(TD::ALIGN_CENTER),
                TD::make('is_active','Active')->render(fn(Product $p)=>$p->is_active?'Yes':'No')->align(TD::ALIGN_CENTER),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Product $p) {
                        return
                            Link::make('Edit')->icon('bs.pencil')->route('platform.products.edit', $p).' '.
                            Button::make('Delete')
                                ->icon('bs.trash')
                                ->confirm('Delete this product?')
                                ->method('remove', ['id' => $p->id]);
                    }),
            ]),
        ];
    }

    public function remove(Request $request)
    {
        $id = $request->get('id');
        $p  = $id ? Product::find($id) : null;

        if (! $p) {
            Toast::warning('Product not found.');
            return back();
        }

        try {
            $p->images()->delete(); // احذف الصور التابعة أولاً إن لزم
            $p->delete();
            Toast::info('Deleted.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: '.$e->getMessage());
        }

        return back();
    }
}
