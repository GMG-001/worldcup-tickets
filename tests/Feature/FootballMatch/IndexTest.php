<?php

namespace Tests\Feature\FootballMatch;

use App\Models\FootballMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsMatchList(): void
    {
        FootballMatch::factory()->count(3)->create();

        $this->getJson('/api/matches')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'home_team_en',
                        'home_team_ka',
                        'away_team_en',
                        'away_team_ka',
                        'stadium_en',
                        'stadium_ka',
                        'match_date'
                    ],
                ],
                'meta',
                'links',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function testIndexReturnsEmptyList(): void
    {
        $this->getJson('/api/matches')
            ->assertStatus(200)
            ->assertJson(['data' => []]);
    }
}
