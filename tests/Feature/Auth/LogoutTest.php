<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogout(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function testLogoutRequiresAuthentication(): void
    {
        $this->postJson('/api/auth/logout')
            ->assertStatus(401);
    }
}
