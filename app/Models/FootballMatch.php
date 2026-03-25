<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FootballMatch extends Model
{
    protected $table = 'matches';

    public function getId(): int
    {
        return $this->id;
    }

    public function getHomeTeamEn(): string
    {
        return $this->home_team_en;
    }

    public function getHomeTeamKa(): string
    {
        return $this->home_team_ka;
    }

    public function getAwayTeamEn(): string
    {
        return $this->away_team_en;
    }

    public function getAwayTeamKa(): string
    {
        return $this->away_team_ka;
    }

    public function getStadiumEn(): string
    {
        return $this->stadium_en;
    }

    public function getStadiumKa(): string
    {
        return $this->stadium_ka;
    }

    public function getMatchDate(): Carbon
    {
        return $this->match_date;
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class, 'match_id');
    }
}
