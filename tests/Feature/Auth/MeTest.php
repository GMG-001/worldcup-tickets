<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function testMeReturnsAuthenticatedUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/auth/me')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id'    => $user->getId(),
                    'email' => $user->getEmail(),
                    'role'  => $user->getRole()->value,
                ],
            ]);

        $this->assertDatabaseHas('users', ['id' => $user->getId()]);
    }

    public function testMeRequiresAuthentication(): void
    {
        $this->getJson('/api/auth/me')
            ->assertStatus(401);
    }
}
