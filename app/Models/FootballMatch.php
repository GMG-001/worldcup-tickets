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

    public function getHomeTeam(): string
    {
        return $this->home_team;
    }

    public function getAwayTeam(): string
    {
        return $this->away_team;
    }

    public function getStadium(): string
    {
        return $this->stadium;
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