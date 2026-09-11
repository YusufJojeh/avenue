<?php

namespace App\Orchid\Screens;

use App\Models\Setting;
use App\Models\Slide;
use App\Http\Controllers\HomeVisibilityController;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class HomeSettingsScreen extends Screen
{
    public function name(): ?string
    {
        return 'Home & Site Settings';
    }

    public function description(): ?string
    {
        return 'تحكم كامل بعناصر الصفحة الرئيسية وبيانات الموقع العامة';
    }

    public function query(): array
    {
        $visibilityController = new HomeVisibilityController();

        return [
            'site_name'  => Setting::get('site.name', 'MyStore'),
            'whatsapp'   => Setting::get('site.whatsapp', '15551234567'),
            'theme_mode' => Setting::get('theme.mode', 'auto'),
            'hero_id'    => (int) Setting::get('home.hero_slide_id', 0),
            'show'       => $visibilityController->getVisibilitySettings(),
            'limits'     => $visibilityController->getSectionLimits(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Save')->icon('check')
                ->method('save')
                ->canSee(auth()->user()->hasAccess('manage.settings')),

            Button::make('Clear Home Cache')->icon('refresh')
                ->method('clearCache')
                ->confirm('سيتم تحديث كاش الصفحة الرئيسية فورًا.')
                ->canSee(auth()->user()->hasAccess('manage.settings')),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('site_name')->title('Site Name')->required(),
                Input::make('whatsapp')->title('WhatsApp Number (E.164)')->help('أرقام فقط، مثال: 15551234567'),
                Select::make('theme_mode')->title('Theme Mode')->options([
                    'auto'  => 'Auto (System)',
                    'light' => 'Light',
                    'dark'  => 'Dark',
                ]),
                Relation::make('hero_id')
                    ->fromModel(Slide::class, 'title', 'id')
                    ->applyScope('current') // Slide::scopeCurrent()
                    ->title('Hero Slide (optional)')
                    ->help('إن تُرك فارغًا سيتم اختيار أول سلايد Position=main'),
            ])->title('General'),

            Layout::rows([
                Switcher::make('show.hero')->sendTrueOrFalse()->title('Show Hero'),
                Switcher::make('show.slider')->sendTrueOrFalse()->title('Show Slider'),
                Switcher::make('show.offers')->sendTrueOrFalse()->title('Show Offers'),
                Switcher::make('show.categories')->sendTrueOrFalse()->title('Show Categories'),
                Switcher::make('show.special')->sendTrueOrFalse()->title('Show Featured'),
                Switcher::make('show.latest')->sendTrueOrFalse()->title('Show Latest'),
                Switcher::make('show.external')->sendTrueOrFalse()->title('Show External Brands'),
            ])->title('Sections toggles'),

            Layout::rows([
                Input::make('limits.slider')->type('number')->title('Slider Items')->min(1)->value(6),
                Input::make('limits.categories')->type('number')->title('Categories')->min(1)->value(8),
                Input::make('limits.special')->type('number')->title('Featured')->min(1)->value(8),
                Input::make('limits.latest')->type('number')->title('Latest')->min(1)->value(12),
                Input::make('limits.external')->type('number')->title('External Brands')->min(1)->value(8),
            ])->title('Limits'),
        ];
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'site_name'  => 'required|string|max:255',
            'whatsapp'   => 'nullable|string|max:32',
            'theme_mode' => 'required|in:auto,light,dark',
            'hero_id'    => 'nullable|integer',
            'show'       => 'array',
            'limits'     => 'array',
        ]);

        $visibilityController = new HomeVisibilityController();

        Setting::set('site.name', $data['site_name']);
        Setting::set('site.whatsapp', $data['whatsapp'] ?? '');
        Setting::set('theme.mode', $data['theme_mode']);
        Setting::set('home.hero_slide_id', (int) ($data['hero_id'] ?? 0));

        // Save visibility settings
        foreach (($data['show'] ?? []) as $k => $v) {
            Setting::set("home.show.$k", (bool) $v);
        }

        // Save limits
        Setting::set('home.limits', $data['limits'] ?? []);

        // Clear visibility cache
        $visibilityController->clearCache();

        $this->bumpHomeCache();

        Toast::info('Saved successfully.');
        return back();
    }

    public function clearCache()
    {
        $visibilityController = new HomeVisibilityController();
        $visibilityController->clearCache();
        $this->bumpHomeCache();
        Toast::info('Home cache refreshed.');
        return back();
    }

    /** يرفع نسخة كاش الصفحة الرئيسية حتى تتحدث فورًا */
    private function bumpHomeCache(): void
    {
        try {
            if (class_exists(\App\Support\HomeCache::class)) {
                \App\Support\HomeCache::bump();
                return;
            }
            // بديل بسيط إن لم يوجد HomeCache helper
            $key = 'home.cache.version';
            cache()->add($key, 1, 86400);
            cache()->increment($key);
        } catch (\Throwable $e) {
            // تجاهل أي خطأ في bump
        }
    }
}
