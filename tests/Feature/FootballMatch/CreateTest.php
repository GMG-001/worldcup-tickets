<?php

namespace Tests\Feature\FootballMatch;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'home_team_en' => 'Brazil',
            'home_team_ka' => 'ბრაზილია',
            'away_team_en' => 'Germany',
            'away_team_ka' => 'გერმანია',
            'stadium_en'   => 'MetLife Stadium',
            'stadium_ka'   => 'მეტლაიფ სტადიონი',
            'match_date'   => now()->addYear()->format('Y-m-d'),
        ];
    }

    public function testAdminCanCreateMatch(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/admin/matches', $this->validPayload())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'home_team_en',
                    'home_team_ka',
                    'away_team_en',
                    'away_team_ka',
                    'stadium_en',
                    'match_date'
                ],
            ]);

        $this->assertDatabaseHas('matches', ['home_team_en' => 'Brazil', 'away_team_en' => 'Germany']);
    }

    public function testCreateRequiresAuthentication(): void
    {
        $this->postJson('/api/admin/matches', $this->validPayload())
            ->assertStatus(401);
    }

    public function testFanCannotCreateMatch(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->postJson('/api/admin/matches', $this->validPayload())
            ->assertStatus(403);
    }

    public function testCreateRequiresAllFields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/admin/matches', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                [
                    'home_team_en',
                    'home_team_ka',
                    'away_team_en',
                    'away_team_ka',
                    'stadium_en',
                    'stadium_ka',
                    'match_date'
                ]
            );
    }

    public function testCreateRequiresFutureMatchDate(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/admin/matches', array_merge($this->validPayload(), [
            'match_date' => now()->subDay()->format('Y-m-d'),
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['match_date']);
    }
}
