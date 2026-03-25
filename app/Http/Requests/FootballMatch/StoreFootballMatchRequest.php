<?php

namespace App\Http\Requests\FootballMatch;

use Illuminate\Foundation\Http\FormRequest;

class StoreFootballMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_team_en' => ['required', 'string', 'max:255'],
            'home_team_ka' => ['required', 'string', 'max:255'],
            'away_team_en' => ['required', 'string', 'max:255'],
            'away_team_ka' => ['required', 'string', 'max:255'],
            'stadium_en'   => ['required', 'string', 'max:255'],
            'stadium_ka'   => ['required', 'string', 'max:255'],
            'match_date'   => ['required', 'date', 'after:now'],
        ];
    }
}
