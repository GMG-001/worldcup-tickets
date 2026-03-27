<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientSeatsException;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Jobs\ProcessPaymentJob;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservationController extends Controller
{
    public function reserve(StoreReservationRequest $request, ReservationService $service): JsonResponse
    {
        try {
            $reservation = $service->reserve($request->user()->getId(), $request->validated());
        } catch (InsufficientSeatsException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ReservationResource($reservation->load('ticketCategory.match')))
            ->response()
            ->setStatusCode(201);
    }

    public function pay(int $id, ReservationService $service): JsonResponse
    {
        $reservation = $service->findOrFail($id);

        return $service->pay($reservation);
    }

    public function cancel(int $id, ReservationService $service): JsonResponse
    {
        $reservation = $service->findOrFail($id);
        $service->cancel($reservation);

        return response()->json(null, 204);
    }
}
