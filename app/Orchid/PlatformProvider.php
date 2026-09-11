<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu; // ✅ موجود مسبقاً
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', '/theme.css');
        $dashboard->registerResource('stylesheets', '/css/platform-theme.css');
        $dashboard->registerResource('stylesheets', '/css/crystal-platform.css');
        $dashboard->registerResource('scripts', '/js/crystal-platform.js');
    }

    /**
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            // ====== الرئيسي
            Menu::make(__('platform.menu.dashboard'))
                ->icon('bs.house')
                ->route('platform.index'),

            // ====== الكاتالوج
            Menu::make(__('platform.menu.catalog'))->title(__('platform.menu.catalog')),

            Menu::make(__('platform.menu.categories'))
                ->icon('bs.collection')
                ->route('platform.categories.list'),

            Menu::make(__('platform.menu.brands'))
                ->icon('bs.tags')
                ->route('platform.brands.list'),

            Menu::make(__('platform.menu.products'))
                ->icon('bs.box')
                ->route('platform.products.list'),

            Menu::make('Product Imports')
                ->icon('bs.file-earmark-spreadsheet')
                ->route('platform.products.import')
                ->permission('platform.index'),

            Menu::make(__('platform.menu.offers'))
                ->icon('bs.ticket-perforated')
                ->route('platform.offers.list'),

            Menu::make(__('platform.menu.slides'))
                ->icon('bs.images')
                ->route('platform.slides.list'),

            // ====== إعدادات الموقع
            Menu::make(__('platform.menu.site'))->title(__('platform.menu.site')),

            Menu::make(__('platform.menu.appearance'))
                ->icon('bs.palette')
                ->route('platform.appearance')
                ->permission('manage.appearance'),

            Menu::make(__('platform.menu.settings'))
                ->icon('bs.gear')
                ->route('platform.settings')
                ->permission('manage.settings'),

            Menu::make(__('platform.menu.home_settings'))
                ->icon('bs.sliders')
                ->route('platform.site.home')
                ->permission('manage.settings'),



            // ====== الصلاحيات
            Menu::make(__('platform.menu.users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('platform.menu.access_controls')),

            Menu::make(__('platform.menu.roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group(__('platform.permissions.system'))
                ->addPermission('platform.systems.roles', __('platform.menu.roles'))
                ->addPermission('platform.systems.users', __('platform.menu.users')),

            ItemPermission::group(__('platform.menu.settings'))
                ->addPermission('manage.settings', __('platform.permissions.manage_settings'))
                ->addPermission('manage.appearance', __('platform.permissions.manage_appearance')),
        ];
    }
}
