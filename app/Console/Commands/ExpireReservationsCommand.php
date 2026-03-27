<?php

namespace App\Console\Commands;

use App\Services\ReservationService;
use Illuminate\Console\Command;

class ExpireReservationsCommand extends Command
{
    protected $signature   = 'reservations:expire';
    protected $description = 'Expire pending reservations whose time window has passed and release seats back to available';

    public function handle(ReservationService $service): int
    {
        $count = $service->expireStale();

        $this->info("Expired {$count} reservation(s) and released their seats.");

        return self::SUCCESS;
    }
}
