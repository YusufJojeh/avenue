<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Create admin role
            $adminRole = Role::firstOrCreate(
                ['slug' => 'admin'],
                [
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'permissions' => [
                        'platform.systems.roles' => 1,
                        'platform.systems.users' => 1,
                        'manage.settings' => 1,
                        'manage.appearance' => 1,
                        'platform.systems.attachments' => 1,
                        'platform.systems.media' => 1,
                    ],
                ]
            );

            // Create regular user role
            $userRole = Role::firstOrCreate(
                ['slug' => 'user'],
                [
                    'name' => 'User',
                    'slug' => 'user',
                    'permissions' => [],
                ]
            );

            // Create or update admin user
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@test.com'],
                [
                    'name' => 'Admin User',
                    'email' => 'admin@test.com',
                    'password' => Hash::make('password'),
                    'is_admin' => true,
                    'permissions' => [
                        'platform.systems.roles' => 1,
                        'platform.systems.users' => 1,
                        'manage.settings' => 1,
                        'manage.appearance' => 1,
                        'platform.systems.attachments' => 1,
                        'platform.systems.media' => 1,
                    ],
                ]
            );

            // Update existing admin user if needed
            if (!$adminUser->is_admin) {
                $adminUser->update(['is_admin' => true]);
            }

            // Ensure admin user has permissions directly
            $adminUser->update([
                'permissions' => [
                    'platform.systems.roles' => 1,
                    'platform.systems.users' => 1,
                    'manage.settings' => 1,
                    'manage.appearance' => 1,
                    'platform.systems.attachments' => 1,
                    'platform.systems.media' => 1,
                ]
            ]);

            // Assign admin role to admin user
            $adminUser->roles()->sync([$adminRole->id]);

            $this->command->info('Admin role and user created successfully!');

        } catch (\Exception $e) {
            Log::error('RoleSeeder failed: ' . $e->getMessage());
            $this->command->error('RoleSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
