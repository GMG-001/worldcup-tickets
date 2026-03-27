<?php

namespace App\Repositories\Interfaces;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    public function getExpiredPending(): Collection;

    public function findOrFail(int $id): Reservation;

    public function lockForUpdate(int $id): ?Reservation;

    public function create(array $data): Reservation;

    public function update(Reservation $reservation, array $data): Reservation;
}
