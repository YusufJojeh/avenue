<?php

namespace App\Orchid\Screens;

use App\Models\Brand;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;   // 👈
use Orchid\Support\Facades\Toast;   // 👈

class BrandListScreen extends Screen
{
    public function name(): ?string { return 'Brands'; }
    public function description(): ?string { return 'Browse and manage brands'; }

    public function query(): array
    {
        return [
            'brands' => Brand::orderBy('sort_order')->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create')->icon('bs.plus')->route('platform.brands.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('brands', [
                TD::make('name')->sort()->filter(TD::FILTER_TEXT),
                TD::make('slug'),
                TD::make('is_external','External')->render(fn(Brand $b) => $b->is_external ? 'Yes' : 'No')->align(TD::ALIGN_CENTER)->sort(),
                TD::make('is_active','Active')->render(fn(Brand $b) => $b->is_active ? 'Yes' : 'No')->align(TD::ALIGN_CENTER)->sort(),
                TD::make('sort_order','Order')->sort(),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (Brand $b) {
                        return
                            Link::make('Edit')
                                ->icon('bs.pencil')
                                ->route('platform.brands.edit', $b)
                            .' '.
                            Button::make('Delete')
                                ->icon('bs.trash')
                                ->confirm('Delete this brand?')
                                ->method('remove', ['id' => $b->id]);
                    }),
            ]),
        ];
    }

    public function remove(Request $request)
    {
        $id = $request->get('id');

        if (! $id || ! $brand = Brand::find($id)) {
            Toast::warning('Brand not found.');
            return redirect()->route('platform.brands.list');
        }

        try {
            $brand->delete();
            Toast::info('Deleted.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: '.$e->getMessage());
        }

        return redirect()->route('platform.brands.list');
    }
}
