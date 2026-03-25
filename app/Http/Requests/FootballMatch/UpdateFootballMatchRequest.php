<?php

namespace App\Http\Requests\FootballMatch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFootballMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_team_en' => ['sometimes', 'string', 'max:255'],
            'home_team_ka' => ['sometimes', 'string', 'max:255'],
            'away_team_en' => ['sometimes', 'string', 'max:255'],
            'away_team_ka' => ['sometimes', 'string', 'max:255'],
            'stadium_en'   => ['sometimes', 'string', 'max:255'],
            'stadium_ka'   => ['sometimes', 'string', 'max:255'],
            'match_date'   => ['sometimes', 'date', 'after:now'],
        ];
    }
}
