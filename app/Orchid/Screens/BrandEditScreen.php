<?php

namespace App\Orchid\Screens;

use App\Models\Brand;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Support\Facades\Toast;

class BrandEditScreen extends Screen
{
    public ?Brand $brand = null;

    public function query(Brand $brand): array
    {
        $this->brand = $brand;

        return [
            'brand' => $brand,
        ];
    }

    public function name(): ?string
    {
        return $this->brand?->exists ? __('brand.titles.edit') : __('brand.titles.create');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('brand.actions.save'))->icon('bs.check')->method('createOrUpdate'),
            Button::make(__('brand.actions.remove'))
                ->icon('bs.trash')
                ->confirm(__('brand.actions.confirm_remove'))
                ->method('remove')
                ->canSee($this->brand?->exists),
        ];
    }

    public function layout(): array
    {
        $logoHelp = $this->brand?->logo_url
            ? __('brand.help.logo_current_prefix') . $this->brand->logo_url
            : __('brand.help.logo');

        return [
            Layout::view('partials.current-image', [
                'image_url'  => $this->brand?->logo_url,
                'image_path' => $this->brand?->logo_path,
                'title'      => __('brand.titles.current_logo')
            ])->canSee($this->brand?->exists && $this->brand?->logo_path),

            Layout::rows([
                Input::make('brand.name')->title(__('brand.fields.name'))->required(),

                Input::make('brand.slug')
                    ->title(__('brand.fields.slug'))
                    ->help(__('brand.help.slug'))
                    ->required(),

                Input::make('logo')
                    ->type('file')
                    ->title(__('brand.fields.logo'))
                    ->acceptedFiles('image/*')
                    ->help($logoHelp),

                Switcher::make('brand.is_external')
                    ->title(__('brand.fields.is_external'))
                    ->sendTrueOrFalse()
                    ->value($this->brand?->exists ? (bool)$this->brand->is_external : false),

                Switcher::make('brand.is_active')
                    ->title(__('brand.fields.is_active'))
                    ->sendTrueOrFalse()
                    ->value($this->brand?->exists ? (bool)$this->brand->is_active : true),

                Input::make('brand.sort_order')
                    ->title(__('brand.fields.sort_order'))
                    ->type('number')
                    ->value($this->brand?->sort_order ?? 0),
            ]),
        ];
    }

    public function createOrUpdate(Request $request, Brand $brand)
    {
        $imageService = app(ImageService::class);

        $rules = [
            'brand.name'        => ['required','string','max:255'],
            'brand.slug'        => ['required','string','max:255', Rule::unique('brands','slug')->ignore($brand->id)],
            'brand.is_external' => ['nullable','boolean'],
            'brand.is_active'   => ['nullable','boolean'],
            'brand.sort_order'  => ['nullable','integer'],
        ];

        // قواعد الشعار من خدمة الصور
        $logoRules = $imageService->getValidationRules('logo', false);
        $rules = array_merge($rules, $logoRules);

        $data = $request->validate($rules);

        if (blank($data['brand']['slug'])) {
            $data['brand']['slug'] = Str::slug($data['brand']['name']);
        }

        $brand->fill($data['brand']);

        // رفع الشعار وتحديث المسار
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $uploadOptions = $imageService->getUploadOptions('brands'); // مثال: ['max' => 3072, 'webp' => true] إلخ

            $result = $imageService->upload($file, 'brands', $uploadOptions);

            if ($result['success'] ?? false) {
                $brand->logo_path = $result['path'];
            } else {
                Toast::error(__('brand.toast.upload_failed', ['error' => $result['error'] ?? 'unknown']));
                return back();
            }
        }

        $brand->save();

        Toast::info(__('brand.toast.saved'));
        return redirect()->route('platform.brands.list');
    }

    public function remove(Brand $brand)
    {
        try {
            $brand->delete();
            Toast::info(__('brand.toast.deleted'));
            return redirect()->route('platform.brands.list');
        } catch (\Throwable $e) {
            Toast::error(__('brand.toast.delete_failed', ['error' => $e->getMessage()]));
            return back();
        }
    }
}
