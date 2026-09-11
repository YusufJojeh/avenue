<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'site.name'],
            ['group' => 'site', 'value' => 'AVENUEBRAND']
        );

        Setting::updateOrCreate(
            ['key' => 'site.logo_light'],
            ['group' => 'site', 'value' => 'logos/logo-light.png']
        );

        Setting::updateOrCreate(
            ['key' => 'site.logo_dark'],
            ['group' => 'site', 'value' => 'logos/logo-dark.png']
        );

        Setting::updateOrCreate(
            ['key' => 'home.limits'],
            ['group' => 'home', 'value' => json_encode([
                'special'    => 12,
                'latest'     => 12,
                'external'   => 12,
                'categories' => 8,
            ])]
        );
    }
}
