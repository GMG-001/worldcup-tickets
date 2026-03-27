<?php

namespace Tests\Feature\FootballMatch;

use App\Models\FootballMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminCanUpdateMatch(): void
    {
        $admin = User::factory()->admin()->create();
        $match = FootballMatch::factory()->create();

        $this->actingAs($admin)->patchJson("/api/admin/matches/{$match->getId()}", [
            'home_team_en' => 'Updated Team',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => ['home_team_en' => 'Updated Team'],
            ]);

        $this->assertDatabaseHas('matches', ['id' => $match->getId(), 'home_team_en' => 'Updated Team']);
    }

    public function testUpdateRequiresAuthentication(): void
    {
        $match = FootballMatch::factory()->create();

        $this->patchJson("/api/admin/matches/{$match->getId()}", ['home_team_en' => 'X'])
            ->assertStatus(401);
    }

    public function testFanCannotUpdateMatch(): void
    {
        $fan   = User::factory()->create();
        $match = FootballMatch::factory()->create();

        $this->actingAs($fan)->patchJson("/api/admin/matches/{$match->getId()}", ['home_team_en' => 'X'])
            ->assertStatus(403);
    }

    public function testUpdateReturns404ForNonExistentMatch(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patchJson('/api/admin/matches/999', ['home_team_en' => 'X'])
            ->assertStatus(404);
    }

    public function testUpdateRejectsPastMatchDate(): void
    {
        $admin = User::factory()->admin()->create();
        $match = FootballMatch::factory()->create();

        $this->actingAs($admin)->patchJson("/api/admin/matches/{$match->getId()}", [
            'match_date' => now()->subDay()->format('Y-m-d H:i:s'),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['match_date']);
    }
}
