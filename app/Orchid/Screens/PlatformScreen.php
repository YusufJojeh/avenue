<?php

namespace App\Orchid\Screens;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PlatformScreen extends Screen
{
    public function name(): ?string
    {
        return __('platform.menu.dashboard');
    }

    public function description(): ?string
    {
        return __('platform.dashboard.description');
    }

    public function query(): iterable
    {
        $socialMedia = [];
        $socialMediaJson = Setting::get('site.social_media');

        if (is_string($socialMediaJson) && $socialMediaJson !== '') {
            $decoded = json_decode($socialMediaJson, true);
            if (is_array($decoded)) {
                $socialMedia = $decoded;
            }
        }

        return [
            // Cached for 5 minutes: these are plain counts, not covered by
            // EnhancedPerformanceService, and were re-run on every dashboard load.
            'stats' => Cache::remember('platform.dashboard.stats', 300, fn () => [
                'products' => Product::count(),
                'categories' => Category::count(),
                'brands' => Brand::count(),
                'offers' => Offer::count(),
            ]),
            // Demo data used by dashboard partial. Replace with real reporting later.
            'sales' => $this->demoSales(app()->getLocale()),
            'social_media' => [
                'facebook' => $socialMedia['facebook'] ?? '',
                'instagram' => $socialMedia['instagram'] ?? '',
            ],
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('platform.dashboard.add_product'))
                ->icon('bs.plus-circle')
                ->route('platform.products.create')
                ->class('btn btn-gradient text-white'),
        ];
    }

    public function layout(): iterable
    {
        $canManage = auth()->user()?->hasAccess('manage.settings') ?? false;

        return [
            Layout::view('platform.partials.dashboard-hero'),
            Layout::view('platform.partials.dashboard-cards'),

            Layout::rows([
                Input::make('social_media.facebook')
                    ->title(__('platform.dashboard.facebook_url'))
                    ->placeholder('https://facebook.com/yourpage'),

                Input::make('social_media.instagram')
                    ->title(__('platform.dashboard.instagram_url'))
                    ->placeholder('https://instagram.com/yourpage'),

                Button::make(__('common.actions.save'))
                    ->icon('bs.check2')
                    ->method('saveSocialLinks')
                    ->class('btn btn-primary'),
            ])
                ->title(__('platform.dashboard.social_links_title'))
                ->canSee($canManage),

            Layout::view('platform.partials.dashboard-sales'),
            Layout::view('platform.partials.dashboard-script'),
        ];
    }

    public function saveSocialLinks(Request $request)
    {
        if (!(auth()->user()?->hasAccess('manage.settings') ?? false)) {
            abort(403);
        }

        $data = $request->validate([
            'social_media.facebook' => ['nullable', 'url'],
            'social_media.instagram' => ['nullable', 'url'],
        ]);

        $socialMedia = [
            'facebook' => $data['social_media']['facebook'] ?? '',
            'instagram' => $data['social_media']['instagram'] ?? '',
        ];

        Setting::set('site.social_media', json_encode($socialMedia));

        Toast::info(__('common.toast.saved'));
    }

    private function demoSales(string $locale): array
    {
        $months = $locale === 'ar'
            ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو']
            : ['January', 'February', 'March', 'April', 'May', 'June'];

        $values = [1200, 1450, 1800, 2100, 2600, 3000];

        $out = [];
        foreach ($months as $i => $m) {
            $out[] = ['month' => $m, 'value' => $values[$i] ?? 0];
        }

        return $out;
    }
}