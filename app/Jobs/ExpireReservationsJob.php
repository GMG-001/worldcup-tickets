<?php

namespace App\Jobs;

use App\Services\ReservationService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireReservationsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Prevent overlapping runs: if a previous expiry sweep is still
     * running when the next minute fires, skip the new dispatch.
     */
    public int $uniqueFor = 120;

    /** Retry up to 3 times on transient exceptions. */
    public int $tries = 3;

    /** Short backoff: next scheduled run is in ~60 s, so keep delays small. */
    public function backoff(): array
    {
        return [5, 15];
    }

    public function handle(ReservationService $service): void
    {
        $service->expireStale();
    }
}
