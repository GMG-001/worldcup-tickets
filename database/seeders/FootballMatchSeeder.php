<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FootballMatchSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $matches = [
            [
                'home_team_en' => 'Brazil',
                'home_team_ka' => 'ბრაზილია',
                'away_team_en' => 'Argentina',
                'away_team_ka' => 'არგენტინა',
                'stadium_en'   => 'MetLife Stadium, New Jersey',
                'stadium_ka'   => 'მეტლაიფ სტადიონი, ნიუ-ჯერსი',
                'match_date'   => '2026-06-14 18:00:00',
            ],
            [
                'home_team_en' => 'France',
                'home_team_ka' => 'საფრანგეთი',
                'away_team_en' => 'England',
                'away_team_ka' => 'ინგლისი',
                'stadium_en'   => 'SoFi Stadium, Los Angeles',
                'stadium_ka'   => 'სოფი სტადიონი, ლოს-ანჯელესი',
                'match_date'   => '2026-06-17 21:00:00',
            ],
            [
                'home_team_en' => 'Spain',
                'home_team_ka' => 'ესპანეთი',
                'away_team_en' => 'Germany',
                'away_team_ka' => 'გერმანია',
                'stadium_en'   => 'AT&T Stadium, Dallas',
                'stadium_ka'   => 'AT&T სტადიონი, დალასი',
                'match_date'   => '2026-06-20 20:00:00',
            ],
            [
                'home_team_en' => 'Portugal',
                'home_team_ka' => 'პორტუგალია',
                'away_team_en' => 'Netherlands',
                'away_team_ka' => 'ნიდერლანდები',
                'stadium_en'   => 'Estadio Azteca, Mexico City',
                'stadium_ka'   => 'ასტეკა სტადიონი, მეხიკო',
                'match_date'   => '2026-06-23 19:00:00',
            ],
            [
                'home_team_en' => 'Italy',
                'home_team_ka' => 'იტალია',
                'away_team_en' => 'Croatia',
                'away_team_ka' => 'ხორვატია',
                'stadium_en'   => 'BC Place, Vancouver',
                'stadium_ka'   => 'ბი-სი ფლეისი, ვანკუვერი',
                'match_date'   => '2026-06-26 17:00:00',
            ],
            [
                'home_team_en' => 'Morocco',
                'home_team_ka' => 'მაროკო',
                'away_team_en' => 'Senegal',
                'away_team_ka' => 'სენეგალი',
                'stadium_en'   => 'Levi\'s Stadium, Santa Clara',
                'stadium_ka'   => 'ლევის სტადიონი, სანტა-კლარა',
                'match_date'   => '2026-06-29 21:00:00',
            ],
            [
                'home_team_en' => 'Japan',
                'home_team_ka' => 'იაპონია',
                'away_team_en' => 'South Korea',
                'away_team_ka' => 'სამხრეთ კორეა',
                'stadium_en'   => 'Hard Rock Stadium, Miami',
                'stadium_ka'   => 'ჰარდ როქ სტადიონი, მაიამი',
                'match_date'   => '2026-07-02 20:00:00',
            ],
            [
                'home_team_en' => 'USA',
                'home_team_ka' => 'აშშ',
                'away_team_en' => 'Mexico',
                'away_team_ka' => 'მექსიკა',
                'stadium_en'   => 'Lincoln Financial Field, Philadelphia',
                'stadium_ka'   => 'ლინქოლნ ფინანშელ ფილდი, ფილადელფია',
                'match_date'   => '2026-07-05 19:00:00',
            ],
        ];

        foreach ($matches as $match) {
            FootballMatch::factory()->create($match);
        }
    }
}
