<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Services\ImageService;
use App\Services\ProductCsvPreviewService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Toast;

class ProductEditScreen extends Screen
{
    // Allow property to be nullable with default value null
    public ?Product $product = null;

    public function query(Product $product): array
    {
        $this->product = $product->load('images', 'sizes', 'category', 'brand');

        return [
            'product' => $this->product,
            'images'  => $this->product->exists
                ? $this->product->images()->orderBy('sort_order')->orderBy('id')->get()
                : collect(),
            'sizes'   => $this->product->exists
                ? $this->product->sizes()->orderBy('sort_order')->orderBy('size')->get()
                : collect(),
        ];
    }

    public function name(): ?string
    {
        return $this->product?->exists ? 'Edit Product' : 'Create Product';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')->icon('bs.check')->method('createOrUpdate'),
            Button::make('Remove')
                ->icon('bs.trash')
                ->confirm('Delete this product?')
                ->method('remove')
                ->canSee($this->product?->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Select::make('product.category_id')
                    ->title('Category')
                    ->fromModel(Category::class, 'name', 'id')
                    ->required(),

                Input::make('product.name')
                    ->title('Name')
                    ->required()
                    ->help('Slug and SKU are auto-generated from the name'),

                Input::make('product.price')
                    ->title('Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
            ])->title('Basic Information'),

            Layout::accordion([
                'Pricing & Stock' => Layout::rows([
                    Input::make('product.sale_price')
                        ->title('Sale Price')
                        ->type('number')
                        ->step('0.01')
                        ->help('Leave empty if no sale'),

                    Input::make('product.stock_qty')
                        ->title('Stock Quantity')
                        ->type('number')
                        ->help('Leave empty for unlimited'),
                ]),

                'Description' => Layout::rows([
                    TextArea::make('product.short_description')
                        ->title('Short Description')
                        ->rows(3),

                    TextArea::make('product.description')
                        ->title('Description')
                        ->rows(10),
                ]),

                'SEO' => Layout::rows([
                    Input::make('product.seo_title_ar')
                        ->title('Arabic SEO title')
                        ->maxlength(ProductCsvPreviewService::SEO_TITLE_MAX),
                    Input::make('product.seo_title_en')
                        ->title('English SEO title')
                        ->maxlength(ProductCsvPreviewService::SEO_TITLE_MAX),
                    TextArea::make('product.seo_description_ar')
                        ->title('Arabic meta description')
                        ->maxlength(ProductCsvPreviewService::SEO_DESCRIPTION_MAX)
                        ->rows(3),
                    TextArea::make('product.seo_description_en')
                        ->title('English meta description')
                        ->maxlength(ProductCsvPreviewService::SEO_DESCRIPTION_MAX)
                        ->rows(3),
                ]),

                'Advanced Options' => Layout::rows([
                    Select::make('product.brand_id')
                        ->title('Brand')
                        ->fromModel(Brand::class, 'name', 'id')
                        ->empty('No Brand'),

                    Input::make('product.slug')
                        ->title('Slug')
                        ->help('Auto-generated from name if left empty'),

                    Input::make('product.sku')
                        ->title('SKU')
                        ->help('Auto-generated if left empty'),

                    Switcher::make('product.is_featured')
                        ->title('Featured')
                        ->sendTrueOrFalse(),

                    Switcher::make('product.is_active')
                        ->title('Active')
                        ->sendTrueOrFalse(),

                    DateTimer::make('product.published_at')
                        ->title('Published At')
                        ->help('Defaults to now if left empty')
                        ->allowInput(),
                ]),
            ]),

            // Product Sizes - For New Products (during creation) - Single field
            Layout::rows([
                TextArea::make('sizes_input')
                    ->title('Product Sizes (Optional)')
                    ->placeholder('Enter sizes separated by commas or new lines. Example: S, M, L, XL or one per line')
                    ->rows(4)
                    ->help('Enter sizes separated by commas (e.g., S, M, L, XL) or one per line. Leave empty if product has no sizes.'),
            ])->title('Product Sizes (Optional)')
                ->canSee(!$this->product?->exists),

            // Add new image (file upload) - Available for both new and existing products
            Layout::block(
                Layout::rows([
                    Input::make('new_image.file')
                        ->type('file')
                        ->title('Image File(s)')
                        ->acceptedFiles('image/*')
                        ->multiple()
                        ->help('You can select multiple images at once (JPG/PNG/WebP/GIF). Max 5MB per image. Hold Ctrl/Cmd to select multiple files.' . (!$this->product?->exists ? ' Images will be uploaded when you save the product.' : '')),

                    Input::make('new_image.alt')->title('Alt Text (optional, applies to all)'),
                    Switcher::make('new_image.is_primary')->title('Set First as Primary?')->sendTrueOrFalse(),
                    Input::make('new_image.sort_order')->title('Starting Order')->type('number')->value(0)
                        ->help('Order number for first image (others will increment automatically)'),

                    Button::make('Add Image(s)')->icon('bs.plus')->method('addImage'),
                ])
            )->title('Product Images (Optional)')
                ->description($this->product?->exists 
                    ? 'Add more images to this product.' 
                    : 'Select images to upload when creating this product. Images will be saved when you click "Save".'),

            // Images table - Only visible for existing products
            Layout::table('images', [
                TD::make('path', 'Preview')->render(function (ProductImage $img) {
                    $url = $img->url;
                    if ($url) {
                        return '<div class="d-flex flex-column align-items-center">
                                    <a href="'.$url.'" target="_blank" class="text-decoration-none">
                                        <img src="'.$url.'" alt="'.e($img->alt ?? '').'" 
                                             style="height:120px;width:120px;object-fit:cover;border-radius:8px;border:2px solid #dee2e6;cursor:pointer;transition:transform 0.2s;"
                                             onmouseover="this.style.transform=\'scale(1.05)\';this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.2)\';"
                                             onmouseout="this.style.transform=\'scale(1)\';this.style.boxShadow=\'none\';"
                                             title="Click to view full size">
                                    </a>
                                    <small class="text-muted mt-2" style="font-size:0.75rem;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'.e($img->path).'">'.e($img->path).'</small>
                                </div>';
                    }
                    return '<span class="text-muted">No image</span>';
                })->width('180')->align(TD::ALIGN_CENTER),

                TD::make('alt','Alt Text')->width('220')->render(fn(ProductImage $img) => e($img->alt ?? '—')),

                TD::make('is_primary','Primary')
                    ->render(function(ProductImage $img) {
                        if ($img->is_primary) {
                            return '<span class="badge bg-success">Primary</span>';
                        }
                        return '<span class="text-muted">—</span>';
                    })
                    ->align(TD::ALIGN_CENTER)
                    ->width('100'),

                TD::make('sort_order','Order')->width('90')->sort(),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->width('240')
                    ->render(function (ProductImage $img) {
                        $buttons = [];
                        if (! $img->is_primary) {
                            $buttons[] = Button::make('Set Primary')
                                ->icon('bs.star')
                                ->method('makePrimary', ['id' => $img->id])
                                ->class('btn-sm');
                        }

                        $buttons[] = Button::make('Delete')
                            ->icon('bs.trash')
                            ->confirm('Delete this image?')
                            ->method('deleteImage', ['id' => $img->id])
                            ->class('btn-sm btn-danger');

                        return implode(' ', array_map(fn($b) => $b->render(), $buttons));
                    }),
            ])->title('Product Images')
                ->canSee($this->product?->exists),

            // Product Sizes Management
            Layout::table('sizes', [
                TD::make('size', 'Size')->width('120'),
                TD::make('price', 'Price')
                    ->render(function (ProductSize $size) {
                        if ($size->price !== null) {
                            return number_format($size->price, 2) . ' ' . config('app.currency', 'USD');
                        }
                        return '<span class="text-muted">Uses product price</span>';
                    })
                    ->width('150'),
                TD::make('sale_price', 'Sale Price')
                    ->render(function (ProductSize $size) {
                        if ($size->sale_price !== null) {
                            return number_format($size->sale_price, 2) . ' ' . config('app.currency', 'USD');
                        }
                        return '<span class="text-muted">—</span>';
                    })
                    ->width('150'),
                TD::make('stock_qty', 'Stock')->width('100'),
                TD::make('sku', 'SKU')->width('150')->render(fn(ProductSize $size) => e($size->sku ?? '—')),
                TD::make('is_active', 'Active')
                    ->render(fn(ProductSize $size) => $size->is_active ? 'Yes' : 'No')
                    ->align(TD::ALIGN_CENTER)
                    ->width('80'),
                TD::make('sort_order', 'Order')->width('80'),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->width('200')
                    ->render(function (ProductSize $size) {
                        $buttons = [];
                        $buttons[] = ModalToggle::make('Edit')
                            ->icon('bs.pencil')
                            ->modal('editSizeModal')
                            ->modalTitle('Edit Size: ' . $size->size)
                            ->method('editSize')
                            ->asyncParameters(['size' => $size->id]);
                        $buttons[] = Button::make('Delete')
                            ->icon('bs.trash')
                            ->confirm('Delete this size?')
                            ->method('deleteSize', ['id' => $size->id]);
                        return implode(' ', array_map(fn($b) => $b->render(), $buttons));
                    }),
            ])->title('Product Sizes (Optional)')
                ->canSee($this->product?->exists),

            // Add new size
            Layout::rows([
                Input::make('new_size.size')
                    ->title('Size')
                    ->placeholder('e.g., S, M, L, XL, 42, 43, etc.')
                    ->help('Size name (required)'),

                Input::make('new_size.price')
                    ->title('Price (Optional)')
                    ->type('number')
                    ->step('0.01')
                    ->help('Leave empty to use product price'),

                Input::make('new_size.sale_price')
                    ->title('Sale Price (Optional)')
                    ->type('number')
                    ->step('0.01')
                    ->help('Leave empty if no sale'),

                Input::make('new_size.stock_qty')
                    ->title('Stock Quantity')
                    ->type('number')
                    ->value(0)
                    ->help('Stock for this size'),

                Input::make('new_size.sku')
                    ->title('SKU (Optional)')
                    ->help('Unique SKU for this size'),

                Input::make('new_size.sort_order')
                    ->title('Order')
                    ->type('number')
                    ->value(0)
                    ->help('Display order'),

                Switcher::make('new_size.is_active')
                    ->title('Active')
                    ->sendTrueOrFalse()
                    ->value(true),

                Button::make('Add Size')->icon('bs.plus')->method('addSize'),
            ])->title('Add New Size')
                ->canSee($this->product?->exists),

            // Edit Size Modal
            Layout::modal('editSizeModal', Layout::rows([
                Input::make('size.size')
                    ->title('Size')
                    ->required()
                    ->help('Size name (e.g., S, M, L, XL, 42, 43, etc.)'),

                Input::make('size.price')
                    ->title('Price (Optional)')
                    ->type('number')
                    ->step('0.01')
                    ->help('Leave empty to use product price'),

                Input::make('size.sale_price')
                    ->title('Sale Price (Optional)')
                    ->type('number')
                    ->step('0.01')
                    ->help('Leave empty if no sale'),

                Input::make('size.stock_qty')
                    ->title('Stock Quantity')
                    ->type('number')
                    ->help('Stock for this size'),

                Input::make('size.sku')
                    ->title('SKU (Optional)')
                    ->help('Unique SKU for this size'),

                Input::make('size.sort_order')
                    ->title('Order')
                    ->type('number')
                    ->help('Display order'),

                Switcher::make('size.is_active')
                    ->title('Active')
                    ->sendTrueOrFalse(),

                Button::make('Update Size')->icon('bs.check')->method('editSize'),
            ]))->title('Edit Size')
                ->applyButton('Update')
                ->closeButton('Cancel')
                ->async('asyncGetSize'),
        ];
    }

    public function createOrUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'product.category_id'        => ['required','exists:categories,id'],
            'product.brand_id'           => ['nullable','exists:brands,id'],
            'product.name'               => ['required','string','max:255'],
            'product.slug'               => ['nullable','string','max:255', Rule::unique('products','slug')->ignore($product->id)],
            'product.sku'                => ['nullable','string','max:255'],
            'product.price'              => ['required','numeric','min:0'],
            'product.sale_price'         => ['nullable','numeric','min:0','lte:product.price'],
            'product.stock_qty'          => ['nullable','integer','min:0'],
            'product.is_featured'        => ['nullable','boolean'],
            'product.is_active'          => ['nullable','boolean'],
            'product.published_at'       => ['nullable','date'],
            'product.short_description'  => ['nullable','string'],
            'product.description'        => ['nullable','string'],
            'product.seo_title_ar'       => ['nullable','string','max:'.ProductCsvPreviewService::SEO_TITLE_MAX],
            'product.seo_title_en'       => ['nullable','string','max:'.ProductCsvPreviewService::SEO_TITLE_MAX],
            'product.seo_description_ar' => ['nullable','string','max:'.ProductCsvPreviewService::SEO_DESCRIPTION_MAX],
            'product.seo_description_en' => ['nullable','string','max:'.ProductCsvPreviewService::SEO_DESCRIPTION_MAX],
        ]);

        if (blank($data['product']['slug'] ?? null)) {
            $data['product']['slug'] = Str::slug($data['product']['name']);
        }

        if (blank($data['product']['sku'] ?? null)) {
            $data['product']['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        if (blank($data['product']['published_at'] ?? null)) {
            $data['product']['published_at'] = now();
        }

        $wasNew = !$product->exists;
        $product->fill($data['product'])->save();

        // Save sizes if provided (for new products during creation)
        if ($wasNew && $request->has('sizes_input')) {
            $sizesInput = trim($request->input('sizes_input', ''));
            if (!empty($sizesInput)) {
                // Parse sizes - support comma-separated or newline-separated
                $sizes = preg_split('/[,\n\r]+/', $sizesInput);
                $sizes = array_map('trim', $sizes);
                $sizes = array_filter($sizes, function($size) {
                    return !empty($size);
                });
                
                foreach ($sizes as $index => $sizeName) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size' => $sizeName,
                        'price' => null, // Use product price
                        'sale_price' => null,
                        'stock_qty' => 0, // Use product stock
                        'sku' => null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Handle image uploads during product creation
        if ($wasNew && $request->hasFile('new_image.file')) {
            $imageService = app(ImageService::class);
            $files = $request->file('new_image.file');
            
            // Handle single file (if not array)
            if (!is_array($files)) {
                $files = [$files];
            }

            $uploadOptions = $imageService->getUploadOptions('products');
            $sortOrder = (int) ($request->input('new_image.sort_order', 0));
            $alt = $request->input('new_image.alt');
            $setPrimary = $request->boolean('new_image.is_primary');
            $uploadedCount = 0;
            $errors = [];

            // Set first image as primary if requested
            if ($setPrimary && count($files) > 0) {
                $product->images()->update(['is_primary' => false]);
            }

            foreach ($files as $index => $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                try {
                    $result = $imageService->upload($file, 'products', $uploadOptions);

                    if (!$result['success']) {
                        $errors[] = $file->getClientOriginalName();
                        continue;
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path'       => $result['path'],
                        'alt'        => $alt,
                        'is_primary' => $setPrimary && $index === 0,
                        'sort_order' => $sortOrder + $index,
                    ]);

                    $uploadedCount++;
                } catch (\Exception $e) {
                    $errors[] = $file->getClientOriginalName();
                }
            }

            if ($uploadedCount > 0) {
                Toast::info('Product saved. ' . $uploadedCount . ' image' . ($uploadedCount > 1 ? 's' : '') . ' added.');
            } else {
                Toast::info('Product saved.');
            }
        } else {
            Toast::info('Saved.');
        }

        return redirect()->route('platform.products.edit', $product);
    }

    public function remove(Product $product)
    {
        try {
            // Images will be automatically deleted via model events
            $product->delete();

            Toast::info('Deleted.');
            return redirect()->route('platform.products.list');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: '.$e->getMessage());
            return back();
        }
    }

    public function addImage(Request $request, Product $product)
    {
        // Check if product exists
        if (!$product->exists) {
            Toast::error('Please save the product first before adding images.');
            return back();
        }

        $imageService = app(ImageService::class);
        $validationRules = $imageService->getValidationRules('new_image.file', true);
        $validationRules['new_image.file'] = ['required']; // Allow multiple files
        $validationRules['new_image.alt'] = ['nullable','string','max:255'];
        $validationRules['new_image.is_primary'] = ['nullable','boolean'];
        $validationRules['new_image.sort_order'] = ['nullable','integer'];

        $validated = $request->validate($validationRules);

        $files = $request->file('new_image.file');
        
        // Handle single file (if not array)
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploadOptions = $imageService->getUploadOptions('products');
        $sortOrder = (int) ($validated['new_image']['sort_order'] ?? 0);
        $alt = $validated['new_image']['alt'] ?? null;
        $setPrimary = $request->boolean('new_image.is_primary');
        $uploadedCount = 0;
        $errors = [];

        // Set first image as primary if requested
        if ($setPrimary && count($files) > 0) {
            $product->images()->update(['is_primary' => false]);
        }

        foreach ($files as $index => $file) {
            if (!$file || !$file->isValid()) {
                $errors[] = ($file ? $file->getClientOriginalName() : 'Unknown file') . ': Invalid file';
                continue;
            }

            try {
                $result = $imageService->upload($file, 'products', $uploadOptions);

                if (!$result['success']) {
                    $errors[] = $file->getClientOriginalName() . ': ' . ($result['error'] ?? 'Upload failed');
                    continue;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $result['path'],
                    'alt'        => $alt,
                    'is_primary' => $setPrimary && $index === 0, // Only first image if setPrimary is true
                    'sort_order' => $sortOrder + $index,
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        // Show appropriate messages
        if ($uploadedCount > 0) {
            $message = $uploadedCount . ' image' . ($uploadedCount > 1 ? 's' : '') . ' added successfully.';
            if (count($errors) > 0) {
                $message .= ' (' . count($errors) . ' failed)';
            }
            Toast::info($message);
        }

        if (count($errors) > 0 && $uploadedCount === 0) {
            Toast::error('Failed to upload images: ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
        }

        if ($uploadedCount === 0 && count($errors) === 0) {
            Toast::error('No valid images were uploaded. Please select at least one image file.');
        }

        return back();
    }

    public function deleteImage(Request $request)
    {
        $id = $request->get('id');
        $image = ProductImage::findOrFail($id);

        try {
            $image->delete(); // This will automatically delete the file via model events
            Toast::info('Image deleted.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete image: ' . $e->getMessage());
        }

        return back();
    }

    public function makePrimary(Request $request)
    {
        $id = $request->get('id');
        $image = ProductImage::findOrFail($id);

        try {
            // Remove primary from all other images of this product
            $image->product->images()->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);

            Toast::info('Primary image updated.');
        } catch (\Throwable $e) {
            Toast::error('Cannot update primary image: ' . $e->getMessage());
        }

        return back();
    }

    public function updateImageOrder(Request $request)
    {
        $orders = $request->input('orders', []);
        
        foreach ($orders as $imageId => $order) {
            ProductImage::where('id', $imageId)
                ->where('product_id', $this->product->id)
                ->update(['sort_order' => (int) $order]);
        }

        Toast::info('Image order updated.');
        return back();
    }

    // Size management methods
    public function addSize(Request $request, Product $product)
    {
        $validated = $request->validate([
            'new_size.size' => ['required', 'string', 'max:50'],
            'new_size.price' => ['nullable', 'numeric', 'min:0'],
            'new_size.sale_price' => ['nullable', 'numeric', 'min:0'],
            'new_size.stock_qty' => ['nullable', 'integer', 'min:0'],
            'new_size.sku' => ['nullable', 'string', 'max:255'],
            'new_size.sort_order' => ['nullable', 'integer', 'min:0'],
            'new_size.is_active' => ['nullable', 'boolean'],
        ]);

        // Validate sale_price is less than price if both are set
        if (isset($validated['new_size']['price']) && isset($validated['new_size']['sale_price'])) {
            if ($validated['new_size']['sale_price'] >= $validated['new_size']['price']) {
                Toast::error('Sale price must be less than regular price.');
                return back();
            }
        }

        ProductSize::create([
            'product_id' => $product->id,
            'size' => $validated['new_size']['size'],
            'price' => $validated['new_size']['price'] ?? null,
            'sale_price' => $validated['new_size']['sale_price'] ?? null,
            'stock_qty' => $validated['new_size']['stock_qty'] ?? 0,
            'sku' => $validated['new_size']['sku'] ?? null,
            'sort_order' => $validated['new_size']['sort_order'] ?? 0,
            'is_active' => $request->boolean('new_size.is_active', true),
        ]);

        Toast::info('Size added successfully.');
        return back();
    }

    public function asyncGetSize(ProductSize $size): array
    {
        return [
            'size' => $size,
        ];
    }

    public function editSize(Request $request, Product $product)
    {
        $sizeId = $request->input('size');
        $size = ProductSize::findOrFail($sizeId);

        // Ensure the size belongs to this product
        if ($size->product_id !== $product->id) {
            Toast::error('Invalid size.');
            return back();
        }

        $validated = $request->validate([
            'size.size' => ['required', 'string', 'max:50'],
            'size.price' => ['nullable', 'numeric', 'min:0'],
            'size.sale_price' => ['nullable', 'numeric', 'min:0'],
            'size.stock_qty' => ['nullable', 'integer', 'min:0'],
            'size.sku' => ['nullable', 'string', 'max:255'],
            'size.sort_order' => ['nullable', 'integer', 'min:0'],
            'size.is_active' => ['nullable', 'boolean'],
        ]);

        $sizeData = $validated['size'];

        // Validate sale_price is less than price if both are set
        if (isset($sizeData['price']) && isset($sizeData['sale_price'])) {
            if ($sizeData['sale_price'] >= $sizeData['price']) {
                Toast::error('Sale price must be less than regular price.');
                return back();
            }
        }

        $size->update($sizeData);

        Toast::info('Size updated successfully.');
        return back();
    }

    public function deleteSize(Request $request)
    {
        $id = $request->get('id');
        $size = ProductSize::findOrFail($id);

        try {
            $size->delete();
            Toast::info('Size deleted successfully.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete size: ' . $e->getMessage());
        }

        return back();
    }
}
