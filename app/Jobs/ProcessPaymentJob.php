<?php

namespace App\Jobs;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /** Keep the lock until the job finishes or this many seconds pass. */
    public int $uniqueFor = 600; // 10 minutes — matches the reservation window

    public function __construct(private readonly int $reservationId)
    {
    }

    public function uniqueId(): int
    {
        return $this->reservationId;
    }

    public function handle(ReservationService $service): void
    {
        $reservation = Reservation::find($this->reservationId);

        if (! $reservation || $reservation->getStatus() !== ReservationStatus::Pending) {
            return;
        }

        if ($reservation->getExpiresAt()->isPast()) {
            $service->expire($reservation);

            return;
        }

        // Simulate payment gateway: 80% success rate
        if (random_int(1, 100) <= 80) {
            $service->confirm($reservation);
        } else {
            $service->cancel($reservation);
        }
    }
}
