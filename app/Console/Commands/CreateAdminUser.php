<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create-user';
    protected $description = 'Create an admin user with proper permissions';

    public function handle()
    {
        $email = 'admin@test.com';
        $password = 'password';

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info('User already exists. Updating permissions...');
        } else {
            $user = User::create([
                'name' => 'Admin User',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info('User created successfully!');
        }

        // Give admin permissions (Orchid platform access)
        $user->permissions = [
            'platform.index' => true,
            'platform.systems.users' => true,
            'platform.systems.roles' => true,
            'manage.settings' => true,
            'manage.appearance' => true,
            'manage.translations' => true,
        ];
        $user->save();

        $this->info('Admin user setup complete!');
        $this->info('Email: ' . $email);
        $this->info('Password: ' . $password);
        $this->info('You can now access the admin panel at: http://127.0.0.1:8000/admin');

        return 0;
    }
}
