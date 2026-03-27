<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\TicketCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            [
                'name'            => 'Category 1 (VIP)',
                'price'           => 500.00,
                'seat_count'      => 500,
                'available_count' => 120,
            ],
            [
                'name'            => 'Category 2 (Standard)',
                'price'           => 200.00,
                'seat_count'      => 2000,
                'available_count' => 850,
            ],
            [
                'name'            => 'Category 3 (Economy)',
                'price'           => 80.00,
                'seat_count'      => 5000,
                'available_count' => 4200,
            ],
        ];

        $footballMatch = new FootballMatch();
        $firstMatch = $footballMatch->first();

        foreach ($categories as $index => $category) {
            TicketCategory::factory()->create(
                [
                    'match_id'        => $firstMatch->getId(),
                    'available_count' => $index === 0 ? 2 : $category['available_count'],
                    ...$category,
                ]
            );
        }

        $footballMatch->where(
            'id',
            '!=',
            $firstMatch->getId()
        )->each(function (FootballMatch $match) use ($categories): void {
            foreach ($categories as $category) {
                TicketCategory::factory()->create([
                    'match_id' => $match->getId(),
                    ...$category,
                ]);
            }
        });
    }
}
