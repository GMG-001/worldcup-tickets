<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@worldcup.test',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::factory()->create([
                'name'  => "Fan User {$i}",
                'email' => "fan{$i}@worldcup.test",
            ]);
        }
    }
}
