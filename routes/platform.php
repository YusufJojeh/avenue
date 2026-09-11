<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\CategoryListScreen;
use App\Orchid\Screens\CategoryEditScreen;
use App\Orchid\Screens\BrandListScreen;
use App\Orchid\Screens\BrandEditScreen;
use App\Orchid\Screens\ProductListScreen;
use App\Orchid\Screens\ProductEditScreen;
use App\Orchid\Screens\ProductImportScreen;
use App\Orchid\Screens\OfferListScreen;
use App\Orchid\Screens\OfferEditScreen;
use App\Orchid\Screens\SlideListScreen;
use App\Orchid\Screens\SlideEditScreen;
use App\Orchid\Screens\SettingScreen;
use App\Orchid\Screens\HomeSettingsScreen;
use App\Orchid\Screens\AppearanceScreen;
use Illuminate\Support\Facades\Route;

use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/
Route::screen('/', PlatformScreen::class)
    ->name('platform.index')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->push(__('Dashboard'), route('platform.index'));
    });
// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');
Route::screen('categories', CategoryListScreen::class)
    ->name('platform.categories.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Categories', route('platform.categories.list')));

// Create
Route::screen('categories/create', CategoryEditScreen::class)
    ->name('platform.categories.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.categories.list')
        ->push('Create', route('platform.categories.create')));

// Edit
Route::screen('categories/{category}/edit', CategoryEditScreen::class)
    ->name('platform.categories.edit')
    ->breadcrumbs(fn (Trail $trail, $category) => $trail
        ->parent('platform.categories.list')
        ->push('Edit', route('platform.categories.edit', $category)));
        Route::screen('brands', BrandListScreen::class)
        ->name('platform.brands.list')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Brands', route('platform.brands.list')));

    // Brands create
    Route::screen('brands/create', BrandEditScreen::class)
        ->name('platform.brands.create')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.brands.list')
            ->push('Create', route('platform.brands.create')));

    // Brands edit
    Route::screen('brands/{brand}/edit', BrandEditScreen::class)
        ->name('platform.brands.edit')
        ->breadcrumbs(fn (Trail $trail, $brand) => $trail
            ->parent('platform.brands.list')
            ->push('Edit', route('platform.brands.edit', $brand)));
            Route::screen('products', ProductListScreen::class)
    ->name('platform.products.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Products', route('platform.products.list')));

// Products create
Route::screen('products/create', ProductEditScreen::class)
    ->name('platform.products.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.products.list')
        ->push('Create', route('platform.products.create')));

Route::screen('products/import', ProductImportScreen::class)
    ->name('platform.products.import')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.products.list')
        ->push('Product Imports', route('platform.products.import')));

// Products edit
Route::screen('products/{product}/edit', ProductEditScreen::class)
    ->name('platform.products.edit')
    ->breadcrumbs(fn (Trail $trail, $product) => $trail
        ->parent('platform.products.list')
        ->push('Edit', route('platform.products.edit', $product)));

// Offers list
Route::screen('offers', OfferListScreen::class)
->name('platform.offers.list')
->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push('Offers', route('platform.offers.list')));

// Offers create
Route::screen('offers/create', OfferEditScreen::class)
->name('platform.offers.create')
->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.offers.list')
    ->push('Create', route('platform.offers.create')));

// Offers edit
Route::screen('offers/{offer}/edit', OfferEditScreen::class)
->name('platform.offers.edit')
->breadcrumbs(fn (Trail $trail, $offer) => $trail
    ->parent('platform.offers.list')
    ->push('Edit', route('platform.offers.edit', $offer)));
    // Slides list
Route::screen('slides', SlideListScreen::class)
->name('platform.slides.list')
->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Slides', route('platform.slides.list')));

// Slides create
Route::screen('slides/create', SlideEditScreen::class)
->name('platform.slides.create')
->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.slides.list')->push('Create', route('platform.slides.create')));

// Slides edit
Route::screen('slides/{slide}/edit', SlideEditScreen::class)
->name('platform.slides.edit')
->breadcrumbs(fn (Trail $trail, $slide) => $trail->parent('platform.slides.list')->push('Edit', route('platform.slides.edit', $slide)));
//Settings
Route::screen('settings', SettingScreen::class)
    ->name('platform.settings')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Settings', route('platform.settings')));

    Route::screen('site/home-settings', HomeSettingsScreen::class)
    ->name('platform.site.home')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Home Settings', route('platform.site.home')));

        Route::screen('appearance', AppearanceScreen::class)
        ->name('platform.appearance')
        ->breadcrumbs(fn ($trail) => $trail
            ->parent('platform.index')
            ->push('Appearance', route('platform.appearance')));


// Backup Management
Route::screen('backups', \App\Orchid\Screens\BackupManagementScreen::class)
    ->name('platform.backups')
    ->breadcrumbs(fn ($trail) => $trail
        ->parent('platform.index')
        ->push('Backup Management', route('platform.backups')));

// Content Versioning
Route::screen('content-versioning', \App\Orchid\Screens\ContentVersioningScreen::class)
    ->name('platform.content-versioning')
    ->breadcrumbs(fn ($trail) => $trail
        ->parent('platform.index')
        ->push('Content Versioning', route('platform.content-versioning')));
