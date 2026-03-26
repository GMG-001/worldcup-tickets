<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request, ReservationService $service): JsonResponse
    {
        return response()->json($service->getByUser($request->user()->getId()));
    }

    public function store(StoreReservationRequest $request, ReservationService $service): JsonResponse
    {
        return response()->json(
            $service->create($request->user()->getId(), $request->validated()),
            201,
        );
    }

    public function show(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return response()->json($reservation->load(['ticketCategory.match']));
    }

    public function update(
        UpdateReservationRequest $request,
        Reservation $reservation,
        ReservationService $service
    ): JsonResponse {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return response()->json($service->update($reservation, $request->validated()));
    }

    public function destroy(Reservation $reservation, ReservationService $service): JsonResponse
    {
        $service->delete($reservation);

        return response()->json(null, 204);
    }
}
