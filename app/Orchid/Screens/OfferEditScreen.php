<?php

namespace App\Orchid\Screens;

use App\Models\Offer;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Toast;

class OfferEditScreen extends Screen
{
    public ?Offer $offer = null;

    public function query(Offer $offer): array
    {
        $this->offer = $offer;

        return [
            'offer' => $offer,
            'products' => Product::active()->get(),
            'selected_products' => $offer->exists ? $offer->products->pluck('id')->toArray() : [],
        ];
    }

    public function name(): ?string
    {
        return $this->offer?->exists ? 'Edit Offer' : 'Create Offer';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')->icon('bs.check')->method('createOrUpdate'),
            Button::make('Remove')
                ->icon('bs.trash')
                ->confirm('Delete this offer?')
                ->method('remove')
                ->canSee($this->offer?->exists),
        ];
    }

    public function layout(): array
    {
        $bannerHelp = $this->offer->banner_url
            ? 'Current: ' . $this->offer->banner_url
            : 'Upload banner (JPG/PNG/WebP). Max 5MB';

        return [
            // Current banner display (only for existing offers)
            Layout::view('partials.current-image', [
                'image_url' => $this->offer?->banner_url,
                'image_path' => $this->offer?->banner_image,
                'title' => 'Current Banner'
            ])->canSee($this->offer?->exists && $this->offer?->banner_image),

            Layout::rows([
                Input::make('offer.title')
                    ->title('Title')
                    ->required(),

                TextArea::make('offer.description')
                    ->title('Description')
                    ->rows(3),

                Select::make('offer.type')
                    ->title('Type')
                    ->options([
                        'percent'       => 'Percent (%)',
                        'fixed'         => 'Fixed amount',
                        'free_shipping' => 'Free shipping',
                    ])
                    ->required()
                    ->help('If free shipping, value is ignored.'),

                Input::make('offer.value')
                    ->title('Value')
                    ->type('number')
                    ->step('0.01')
                    ->help('For percent: 1–100. For fixed: >= 0'),

                DateTimer::make('offer.starts_at')->title('Starts at')->allowInput(),
                DateTimer::make('offer.ends_at')->title('Ends at')->allowInput(),

                Switcher::make('offer.is_active')->title('Active')->sendTrueOrFalse()->value($this->offer?->exists ? (bool)$this->offer->is_active : true),

                // Upload banner (instead of path)
                Input::make('banner')
                    ->type('file')
                    ->acceptedFiles('image/*')
                    ->title('Banner Image')
                    ->help($bannerHelp),

                Input::make('offer.cta_url')
                    ->title('CTA URL')
                    ->placeholder('/products'),

                // Attach products
                Select::make('product_ids')
                    ->title('Products')
                    ->fromModel(Product::class, 'name', 'id')
                    ->multiple()
                    ->help('Select products for this offer'),
            ])->title('Offer details'),
        ];
    }

    public function createOrUpdate(Request $request, Offer $offer)
    {
        $imageService = app(ImageService::class);

        $validationRules = [
            'offer.title'       => ['required','string','max:255'],
            'offer.description' => ['nullable','string'],
            'offer.type'        => ['required', Rule::in(['percent','fixed','free_shipping'])],
            'offer.value'       => ['nullable','numeric','min:0'],
            'offer.starts_at'   => ['nullable','date'],
            'offer.ends_at'     => ['nullable','date','after_or_equal:offer.starts_at'],
            'offer.is_active'   => ['nullable','boolean'],
            'offer.cta_url'     => ['nullable','string','max:255'],
            'product_ids'       => ['nullable','array'],
            'product_ids.*'     => ['exists:products,id'],
        ];

        // Add banner validation rules
        $bannerRules = $imageService->getValidationRules('banner', false);
        $validationRules = array_merge($validationRules, $bannerRules);

        $data = $request->validate($validationRules);

        $offer->fill($data['offer']);

        // Store banner in public/offers and delete old one when replacing
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $uploadOptions = $imageService->getUploadOptions('offers');

            $result = $imageService->upload($file, 'offers', $uploadOptions);

            if ($result['success']) {
                $offer->banner_image = $result['path'];
            } else {
                Toast::error('Banner upload failed: ' . $result['error']);
                return back();
            }
        }

        $offer->save();

        // sync products
        $offer->products()->sync($data['product_ids'] ?? []);

        Toast::info('Saved.');
        return redirect()->route('platform.offers.edit', $offer);
    }

    public function remove(Offer $offer)
    {
        try {
            // Banner will be automatically deleted via model events
            $offer->products()->detach();
            $offer->delete();

            Toast::info('Deleted.');
            return redirect()->route('platform.offers.list');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: ' . $e->getMessage());
            return back();
        }
    }
}
