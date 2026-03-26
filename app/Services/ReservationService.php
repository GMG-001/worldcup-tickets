<?php

namespace App\Services;

use App\Models\Reservation;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $repository,
    ) {
    }

    public function getByUser(int $userId): Collection
    {
        return $this->repository->getByUser($userId);
    }

    public function create(int $userId, array $data): Reservation
    {
        return $this->repository->create([
            'user_id'    => $userId,
            'expires_at' => now()->addMinutes(15),
            ...$data,
        ]);
    }

    public function update(Reservation $reservation, array $data): Reservation
    {
        return $this->repository->update($reservation, $data);
    }

    public function delete(Reservation $reservation): void
    {
        $this->repository->delete($reservation);
    }
}
