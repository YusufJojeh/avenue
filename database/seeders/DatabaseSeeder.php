<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            OrchidAdminSeeder::class, // Add this first to ensure admin accounts exist
            RoleSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            OfferSeeder::class,
            SlideSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
