<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;   // 👈 أضف هذا
use Orchid\Support\Facades\Toast;   // 👈 وأضف هذا

class CategoryListScreen extends Screen
{
    public function name(): ?string { return 'Categories'; }
    public function description(): ?string { return 'Browse and manage categories'; }

    public function query(): array
    {
        return [
            'categories' => Category::with('parent')
                ->orderBy('sort_order')
                ->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create')->icon('bs.plus')->route('platform.categories.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('categories', [
                TD::make('image_path','Image')
                    ->render(function (Category $c) {
                        if ($c->image_path) {
                            return '<img src="' . $c->image_url . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" onerror="this.style.display=\'none\';">';
                        }
                        return '<div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">📁</div>';
                    })
                    ->align(TD::ALIGN_CENTER),
                TD::make('name')->sort()->filter(TD::FILTER_TEXT),
                TD::make('parent.name','Parent')->sort(),
                TD::make('is_active','Active')->render(fn(Category $c) => $c->is_active ? 'Yes' : 'No')->align(TD::ALIGN_CENTER),
                TD::make('sort_order','Order')->sort(),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Category $c) {
                        return
                            Link::make('Edit')
                                ->icon('bs.pencil')
                                ->route('platform.categories.edit', $c)
                            .' '.
                            Button::make('Delete')
                                ->icon('bs.trash')
                                ->confirm('Delete this category?')
                                ->method('remove', ['id' => $c->id]);
                    }),
            ]),
        ];
    }

    // 👇 ميثود الحذف من الجدول
    public function remove(Request $request)
    {
        $id = $request->get('id');

        if (! $id || ! $cat = Category::find($id)) {
            Toast::warning('Category not found.');
            return redirect()->route('platform.categories.list');
        }

        try {
            $cat->delete();
            Toast::info('Deleted.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: '.$e->getMessage());
        }

        return redirect()->route('platform.categories.list');
    }
}
