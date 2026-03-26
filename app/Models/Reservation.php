<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $casts = [
        'status'     => ReservationStatus::class,
        'expires_at' => 'datetime',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTicketCategoryId(): int
    {
        return $this->ticket_category_id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function getExpiresAt(): ?Carbon
    {
        return $this->expires_at;
    }

    public function getIdempotencyKey(): string
    {
        return $this->idempotency_key;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }
}
