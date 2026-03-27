<?php

namespace Database\Factories;

use App\Models\FootballMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FootballMatch>
 */
class FootballMatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'home_team_en' => fake()->country(),
            'home_team_ka' => fake()->country(),
            'away_team_en' => fake()->country(),
            'away_team_ka' => fake()->country(),
            'stadium_en'   => fake()->company() . ' Stadium',
            'stadium_ka'   => fake()->company() . ' სტადიონი',
            'match_date'   => fake()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
