<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Support\Facades\Toast;

class CategoryEditScreen extends Screen
{
    public ?Category $category = null;

    public function query(Category $category): array
    {
        $this->category = $category;
        return [ 'category' => $category ];
    }

    public function name(): ?string
    {
        return $this->category?->exists ? 'تعديل التصنيف' : 'إضافة تصنيف جديد';
    }

    public function commandBar(): array
    {
        return [
            Button::make('حفظ')
                ->icon('bs.check')
                ->method('createOrUpdate'),

            Button::make('حذف')
                ->icon('bs.trash')
                ->confirm('هل أنت متأكد أنك تريد حذف هذا التصنيف؟')
                ->method('remove')
                ->canSee($this->category?->exists),
        ];
    }

    public function layout(): array
    {
        $imageHelp = $this->category?->image_url
            ? 'الصورة الحالية: ' . $this->category->image_url
            : 'قم برفع صورة للتصنيف (JPG/PNG/WebP). الحجم الأقصى 3MB';

        return [
            Layout::view('partials.current-image', [
                'image_url' => $this->category?->image_url,
                'image_path' => $this->category?->image_path,
                'title' => 'الصورة الحالية'
            ])->canSee($this->category?->exists && $this->category?->image_path),

            Layout::rows([
                Select::make('category.parent_id')
                    ->title('التصنيف الأب')
                    ->empty('— بدون —')
                    ->fromModel(Category::class, 'name', 'id')
                    ->help('يمكنك تركه فارغ إذا لم يكن للتصنيف أب'),

                Input::make('category.name')
                    ->title('اسم التصنيف')
                    ->required(),

                Input::make('category.slug')
                    ->title('الرابط (Slug)')
                    ->help('معرّف URL فريد')
                    ->required(),

                TextArea::make('category.description')
                    ->title('الوصف')
                    ->rows(3),

                Input::make('image')
                    ->type('file')
                    ->title('الصورة')
                    ->acceptedFiles('image/*')
                    ->help($imageHelp),

                Switcher::make('category.is_active')
                    ->title('مفعل')
                    ->sendTrueOrFalse()
                    ->value($this->category?->exists ? (bool)$this->category->is_active : true),

                Input::make('category.sort_order')
                    ->title('الترتيب')
                    ->type('number')
                    ->value((int)($this->category->sort_order ?? 0)),
            ]),
        ];
    }

    public function createOrUpdate(Request $request, Category $category)
    {
        $imageService = app(ImageService::class);

        $validationRules = [
            'category.parent_id'   => ['nullable','exists:categories,id'],
            'category.name'        => ['required','string','max:255'],
            'category.slug'        => [
                'required','string','max:255',
                Rule::unique('categories','slug')->ignore($category->id),
            ],
            'category.description' => ['nullable','string'],
            'category.is_active'   => ['nullable','boolean'],
            'category.sort_order'  => ['nullable','integer'],
        ];

        $imageRules = $imageService->getValidationRules('image', false);
        $validationRules = array_merge($validationRules, $imageRules);

        $data = $request->validate($validationRules);

        if (blank($data['category']['slug'])) {
            $data['category']['slug'] = Str::slug($data['category']['name']);
        }

        if (!empty($data['category']['parent_id']) && (int)$data['category']['parent_id'] === (int)$category->id) {
            return back()->withErrors(['category.parent_id' => 'لا يمكن أن يكون التصنيف أبًا لنفسه.']);
        }

        $category->fill($data['category']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadOptions = $imageService->getUploadOptions('categories');

            $result = $imageService->upload($file, 'categories', $uploadOptions);

            if ($result['success']) {
                $category->image_path = $result['path'];
            } else {
                Toast::error('فشل رفع الصورة: ' . $result['error']);
                return back();
            }
        }

        $category->save();

        Toast::info('تم الحفظ بنجاح.');
        return redirect()->route('platform.categories.list');
    }

    public function remove(Category $category)
    {
        try {
            $category->delete();
            Toast::info('تم الحذف بنجاح.');
            return redirect()->route('platform.categories.list');
        } catch (\Throwable $e) {
            Toast::error('تعذر الحذف: ' . $e->getMessage());
            return back();
        }
    }
}
