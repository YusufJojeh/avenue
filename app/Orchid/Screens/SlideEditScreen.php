<?php

namespace App\Orchid\Screens;

use App\Models\Slide;
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

class SlideEditScreen extends Screen
{
    public ?Slide $slide = null;

    public function query(Slide $slide): array
    {
        $this->slide = $slide;

        return [
            'slide' => $slide,
        ];
    }

    public function name(): ?string
    {
        return $this->slide?->exists ? 'Edit Slide' : 'Create Slide';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')->icon('bs.check')->method('createOrUpdate'),
            Button::make('Remove')
                ->icon('bs.trash')
                ->confirm('Delete this slide?')
                ->method('remove')
                ->canSee($this->slide?->exists),
        ];
    }

    public function layout(): array
    {
        $imageHelp = $this->slide?->image_url
            ? 'Current: ' . $this->slide->image_url
            : 'Upload image (JPG/PNG/WebP). Max 5MB';

        return [
            // Current image display (only for existing slides)
            Layout::view('partials.current-image', [
                'image_url' => $this->slide?->image_url,
                'image_path' => $this->slide?->image_path,
                'title' => 'Current Image'
            ])->canSee($this->slide?->exists && $this->slide?->image_path),

            Layout::rows([
                Select::make('slide.position')
                    ->title('Position')
                    ->options([
                        'main'   => 'Main hero',
                        'slider' => 'Slider',
                    ])
                    ->required(),

                Input::make('slide.title')->title('Title')->required(),
                Input::make('slide.subtitle')->title('Subtitle'),

                // Upload image instead of text path
                Input::make('image')
                    ->type('file')
                    ->acceptedFiles('image/*')
                    ->title('Image')
                    ->help($imageHelp),

                Input::make('slide.cta_label')->title('CTA label')->placeholder('Shop Now'),
                Input::make('slide.cta_url')->title('CTA URL')->placeholder('/products'),

                Input::make('slide.sort_order')->title('Order')->type('number')->value($this->slide?->sort_order ?? 0),

                DateTimer::make('slide.starts_at')->title('Starts at')->allowInput(),
                DateTimer::make('slide.ends_at')->title('Ends at')->allowInput(),

                Switcher::make('slide.is_active')->title('Active')->sendTrueOrFalse()->value($this->slide?->exists ? (bool)$this->slide->is_active : true),
            ])->title('Slide details'),
        ];
    }

    public function createOrUpdate(Request $request, Slide $slide)
    {
        $imageService = app(ImageService::class);

        $validationRules = [
            'slide.position'   => ['required', Rule::in(['main','slider'])],
            'slide.title'      => ['required','string','max:255'],
            'slide.subtitle'   => ['nullable','string','max:255'],
            'slide.cta_label'  => ['nullable','string','max:255'],
            'slide.cta_url'    => ['nullable','string','max:255'],
            'slide.sort_order' => ['nullable','integer'],
            'slide.starts_at'  => ['nullable','date'],
            'slide.ends_at'    => ['nullable','date','after_or_equal:slide.starts_at'],
            'slide.is_active'  => ['nullable','boolean'],
        ];

        // Add image validation rules
        $imageRules = $imageService->getValidationRules('image', false);
        $validationRules = array_merge($validationRules, $imageRules);

        $data = $request->validate($validationRules);

        $slide->fill($data['slide']);

        // If image is uploaded, store it and delete the old one
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadOptions = $imageService->getUploadOptions('slides');

            $result = $imageService->upload($file, 'slides', $uploadOptions);

            if ($result['success']) {
                $slide->image_path = $result['path'];
            } else {
                Toast::error('Image upload failed: ' . $result['error']);
                return back();
            }
        }

        $slide->save();

        Toast::info('Saved.');
        return redirect()->route('platform.slides.edit', $slide);
    }

    public function remove(Slide $slide)
    {
        try {
            // Image will be automatically deleted via model events
            $slide->delete();

            Toast::info('Deleted.');
            return redirect()->route('platform.slides.list');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: ' . $e->getMessage());
            return back();
        }
    }
}
