<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\TicketStatus;
use App\Jobs\ProcessPaymentJob;
use App\Models\Reservation;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationService extends BaseService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $repository,
        private readonly TicketCategoryRepositoryInterface $categoryRepository,
        private readonly TicketRepositoryInterface $ticketRepository,
    ) {
    }

    public function findOrFail(int $id): Reservation
    {
        return $this->repository->findOrFail($id);
    }

    public function reserve(int $userId, array $data): Reservation
    {
        return DB::transaction(function () use ($userId, $data): Reservation {
            $this->categoryRepository->decrementAvailableOrFail(
                $data['ticket_category_id'],
                $data['quantity']
            );

            return $this->repository->create([
                'user_id'            => $userId,
                'ticket_category_id' => $data['ticket_category_id'],
                'quantity'           => $data['quantity'],
                'status'             => ReservationStatus::Pending,
                'expires_at'         => now()->addMinutes(10),
                'idempotency_key'    => $data['idempotency_key'],
            ]);
        });
    }

    public function confirm(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation): void {
            $locked = $this->repository->lockForUpdate($reservation->getId());

            if (! $locked || $locked->getStatus() !== ReservationStatus::Pending) {
                return;
            }

            $this->repository->update($locked, ['status' => ReservationStatus::Confirmed]);

            $category = $locked->ticketCategory;

            for ($i = 1; $i <= $locked->getQuantity(); $i++) {
                $this->ticketRepository->create([
                    'user_id'        => $locked->getUserId(),
                    'reservation_id' => $locked->getId(),
                    'match_id'       => $category->getMatchId(),
                    'category_id'    => $locked->getTicketCategoryId(),
                    'seat_number'    => $this->generateSeatNumber($i),
                    'status'         => TicketStatus::Issued,
                    'qr_code'        => Str::uuid()->toString(),
                ]);
            }
        });
    }

    public function pay(Reservation $reservation): JsonResponse
    {
        $this->authorize('pay', $reservation);

        if ($reservation->getExpiresAt()->isPast()) {
            return response()->json(['message' => 'Reservation has expired.'], 422);
        }

        ProcessPaymentJob::dispatch($reservation->getId());

        return response()->json(['message' => 'Payment is being processed.'], 202);
    }

    public function cancel(Reservation $reservation): void
    {
        $this->authorize('cancel', $reservation);

        DB::transaction(function () use ($reservation): void {
            $locked = $this->repository->lockForUpdate($reservation->getId());

            if (! $locked || $locked->getStatus() !== ReservationStatus::Pending) {
                return;
            }

            $this->repository->update($locked, ['status' => ReservationStatus::Cancelled]);
            $this->categoryRepository->incrementAvailable(
                $locked->getTicketCategoryId(),
                $locked->getQuantity()
            );
        });
    }

    public function expire(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation): void {
            $locked = $this->repository->lockForUpdate($reservation->getId());

            if (! $locked || $locked->getStatus() !== ReservationStatus::Pending) {
                return;
            }

            $this->repository->update($locked, ['status' => ReservationStatus::Expired]);
            $this->categoryRepository->incrementAvailable(
                $locked->getTicketCategoryId(),
                $locked->getQuantity()
            );
        });
    }

    public function expireStale(): int
    {
        $expired = $this->repository->getExpiredPending();

        foreach ($expired as $reservation) {
            $this->expire($reservation);
        }

        return $expired->count();
    }

    private function generateSeatNumber(int $index): string
    {
        $row  = chr(64 + (int) ceil($index / 10));
        $seat = (($index - 1) % 10) + 1;

        return "{$row}{$seat}";
    }
}
