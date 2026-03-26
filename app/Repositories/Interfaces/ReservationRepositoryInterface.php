<?php

namespace App\Repositories\Interfaces;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    public function getByUser(int $userId): Collection;

    public function create(array $data): Reservation;

    public function update(Reservation $reservation, array $data): Reservation;

    public function delete(Reservation $reservation): void;
}