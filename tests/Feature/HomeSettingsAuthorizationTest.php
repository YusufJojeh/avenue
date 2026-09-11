<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeSettingsAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->json('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('role_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->primary(['user_id', 'role_id']);
        });
    }

    public function test_guest_cannot_mutate_home_settings(): void
    {
        $this->postJson('/api/home/visibility/update', ['section' => 'hero', 'visible' => false])
            ->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_mutate_home_settings(): void
    {
        $user = User::create($this->userAttributes([]));

        $this->actingAs($user)
            ->postJson('/api/home/visibility/update', ['section' => 'hero', 'visible' => false])
            ->assertForbidden();
    }

    public function test_user_with_permission_can_mutate_home_settings(): void
    {
        $user = User::create($this->userAttributes(['manage.settings' => true]));

        $this->actingAs($user)
            ->postJson('/api/home/visibility/update', ['section' => 'hero', 'visible' => false])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('settings', ['key' => 'home.show.hero', 'value' => '0']);
    }

    private function userAttributes(array $permissions): array
    {
        return [
            'name' => 'Test User',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'permissions' => $permissions,
        ];
    }
}
