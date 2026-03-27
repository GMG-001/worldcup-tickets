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

    public function handle(ReservationService $service): void
    {
        $service->expireStale();
    }
}
