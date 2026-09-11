<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\User;
use Illuminate\Support\Facades\Hash;

class OrchidAdminSeeder extends Seeder
{
    /**
     * The default admin permissions array
     */
    protected array $adminPermissions = [
        // System permissions
        'platform.index' => true,
        'platform.systems.roles' => true,
        'platform.systems.users' => true,
        
        // Settings permissions
        'manage.settings' => true,
        'manage.appearance' => true,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin account
        User::create([
            'name'           => 'Admin',
            'email'         => 'admin@example.com',
            'password'      => Hash::make('Admin@123456'),
            'permissions'   => $this->adminPermissions,
        ]);

        // Create backup admin account
        User::create([
            'name'           => 'Backup Admin',
            'email'         => 'backup@example.com',
            'password'      => Hash::make('Backup@123456'),
            'permissions'   => $this->adminPermissions,
        ]);

        $this->command->info('Admin accounts created successfully!');
        $this->command->info('----------------------------------------');
        $this->command->info('Main Admin Account:');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: Admin@123456');
        $this->command->info('----------------------------------------');
        $this->command->info('Backup Admin Account:');
        $this->command->info('Email: backup@example.com');
        $this->command->info('Password: Backup@123456');
        $this->command->info('----------------------------------------');
        $this->command->info('Permissions granted:');
        foreach(array_keys($this->adminPermissions) as $permission) {
            $this->command->info("- $permission");
        }
    }
}
