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
    public int $uniqueFor = 600;

    /** Retry up to 3 times on transient exceptions (DB errors, timeouts, etc.). */
    public int $tries = 3;

    /** Exponential backoff: wait 10 s, then 30 s, then 60 s between attempts. */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(private readonly int $reservationId)
    {
    }

    public function uniqueId(): int
    {
        return $this->reservationId;
    }

    /**
     * When all retries are exhausted, cancel the reservation so the seats
     * are freed immediately rather than waiting for the expiry sweep.
     */
    public function failed(\Throwable $exception): void
    {
        $reservation = Reservation::find($this->reservationId);

        if ($reservation && $reservation->getStatus() === ReservationStatus::Pending) {
            app(ReservationService::class)->cancel($reservation);
        }
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
