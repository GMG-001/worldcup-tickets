<?php

namespace App\Policies;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->getId() === $reservation->getUserId();
    }

    public function pay(User $user, Reservation $reservation): bool
    {
        return $user->getId() === $reservation->getUserId()
            && $reservation->getStatus() === ReservationStatus::Pending;
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->getId() === $reservation->getUserId()
            && $reservation->getStatus() === ReservationStatus::Pending;
    }
}
