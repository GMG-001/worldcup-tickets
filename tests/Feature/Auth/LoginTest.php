<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin(): void
    {
        User::factory()->create([
            'email'    => 'john@example.com',
            'password' => Hash::make('Password1!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => 'john@example.com',
            'password' => 'Password1!',
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'role'],
                'token',
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
        ]);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        User::factory()->create([
            'email'    => 'john@example.com',
            'password' => Hash::make('Password1!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => 'john@example.com',
            'password' => 'WrongPassword!',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function testLoginFailsWithNonExistentEmail(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'Password1!',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function testLoginRequiresEmailAndPassword(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        $this->assertDatabaseEmpty('personal_access_tokens');
    }
}
