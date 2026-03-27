<?php

namespace Tests\Feature\FootballMatch;

use App\Models\FootballMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function testShowReturnsMatch(): void
    {
        $match = FootballMatch::factory()->create();

        $this->getJson("/api/matches/{$match->getId()}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id'           => $match->getId(),
                    'home_team_en' => $match->getHomeTeamEn(),
                    'away_team_en' => $match->getAwayTeamEn(),
                    'stadium_en'   => $match->getStadiumEn(),
                ],
            ]);
    }

    public function testShowReturns404ForNonExistentMatch(): void
    {
        $this->getJson('/api/matches/999')
            ->assertStatus(404);
    }
}
