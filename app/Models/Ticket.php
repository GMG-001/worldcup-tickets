<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getReservationId(): int
    {
        return $this->reservation_id;
    }

    public function getMatchId(): int
    {
        return $this->match_id;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getSeatNumber(): string
    {
        return $this->seat_number;
    }

    public function getStatus(): TicketStatus
    {
        return $this->status;
    }

    public function getQrCode(): string
    {
        return $this->qr_code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }
}
