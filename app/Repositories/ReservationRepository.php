<?php

namespace App\Repositories;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getByUser(int $userId): Collection
    {
        return Reservation::with(['ticketCategory.match'])
            ->where('user_id', $userId)
            ->get();
    }

    public function getExpiredPending(): Collection
    {
        return Reservation::where('status', ReservationStatus::Pending)
            ->where('expires_at', '<', now())
            ->get();
    }

    public function lockForUpdate(int $id): ?Reservation
    {
        return Reservation::lockForUpdate()->find($id);
    }

    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function update(Reservation $reservation, array $data): Reservation
    {
        $reservation->update($data);

        return $reservation;
    }

    public function delete(Reservation $reservation): void
    {
        $reservation->delete();
    }
}
