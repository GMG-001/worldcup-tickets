<?php

namespace App\Repositories;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getExpiredPending(): Collection
    {
        $model = $this->getModel();

        return $model->where('status', ReservationStatus::Pending)
            ->where('expires_at', '<', now())
            ->get();
    }

    public function findOrFail(int $id): Reservation
    {
        $model = $this->getModel();

        return $model->with(
            [
                'ticketCategory.match'
            ]
        )->findOrFail($id);
    }

    public function lockForUpdate(int $id): ?Reservation
    {
        $model = $this->getModel();

        return $model->find($id);
    }

    public function create(array $data): Reservation
    {
        $model = $this->getModel();

        return $model->create($data);
    }

    public function update(Reservation $reservation, array $data): Reservation
    {
        $reservation->update($data);

        return $reservation;
    }

    public function getModel(): Reservation
    {
        return new Reservation();
    }
}
