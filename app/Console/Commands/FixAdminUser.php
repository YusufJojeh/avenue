<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Orchid\Platform\Models\Role;

class FixAdminUser extends Command
{
    protected $signature = 'admin:fix';
    protected $description = 'Fix admin user permissions';

    public function handle()
    {
        $this->info('Fixing admin user permissions...');

        // Find or create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        // Update admin user permissions
        $adminUser->update([
            'is_admin' => true,
            'permissions' => [
                'platform.index' => 1,
                'platform.systems.roles' => 1,
                'platform.systems.users' => 1,
                'manage.settings' => 1,
                'manage.appearance' => 1,
                'platform.systems.attachments' => 1,
                'platform.systems.media' => 1,
                'platform.categories' => 1,
                'platform.brands' => 1,
                'platform.products' => 1,
                'platform.offers' => 1,
                'platform.slides' => 1,
                'platform.translations' => 1,
                'platform.backups' => 1,
                'platform.content-versioning' => 1,
            ]
        ]);

        // Find or create admin role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => [
                    'platform.index' => 1,
                    'platform.systems.roles' => 1,
                    'platform.systems.users' => 1,
                    'manage.settings' => 1,
                    'manage.appearance' => 1,
                    'platform.systems.attachments' => 1,
                    'platform.systems.media' => 1,
                    'platform.categories' => 1,
                    'platform.brands' => 1,
                    'platform.products' => 1,
                    'platform.offers' => 1,
                    'platform.slides' => 1,
                    'platform.translations' => 1,
                    'platform.backups' => 1,
                    'platform.content-versioning' => 1,
                ],
            ]
        );

        // Assign role to user
        $adminUser->roles()->sync([$adminRole->id]);

        $this->info('Admin user permissions fixed successfully!');
        $this->info('Email: admin@test.com');
        $this->info('Password: password');
        
        return 0;
    }
}
