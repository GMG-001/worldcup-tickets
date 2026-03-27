<?php

namespace Database\Factories;

use App\Models\FootballMatch;
use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    public function definition(): array
    {
        $seatCount = fake()->numberBetween(100, 5000);

        return [
            'match_id'        => FootballMatch::factory(),
            'name'            => fake()->randomElement(
                [
                    'Category 1 (VIP)',
                    'Category 2 (Standard)',
                    'Category 3 (Economy)'
                ]
            ),
            'price'           => fake()->randomElement([500.00, 200.00, 80.00]),
            'seat_count'      => $seatCount,
            'available_count' => fake()->numberBetween(0, $seatCount),
        ];
    }
}
