<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCategory extends Model
{
    public function getId(): int
    {
        return $this->id;
    }

    public function getMatchId(): int
    {
        return $this->match_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getSeatCount(): int
    {
        return $this->seat_count;
    }

    public function getAvailableCount(): int
    {
        return $this->available_count;
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }
}