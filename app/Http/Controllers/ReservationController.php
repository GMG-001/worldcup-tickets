<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['ticketCategory.match'])
            ->where('user_id', $request->user()->getId())
            ->get();

        return response()->json($reservations);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        $reservation = Reservation::create([
            'user_id'    => $request->user()->getId(),
            'expires_at' => now()->addMinutes(15),
            ...$request->validated(),
        ]);

        return response()->json($reservation, 201);
    }

    public function show(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return response()->json($reservation->load(['ticketCategory.match']));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        $reservation->update($request->validated());

        return response()->json($reservation);
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $reservation->delete();

        return response()->json(null, 204);
    }
}
