<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservationController extends Controller
{
    public function index(Request $request, ReservationService $service): AnonymousResourceCollection
    {
        return ReservationResource::collection($service->getByUser($request->user()->getId()));
    }

    public function store(StoreReservationRequest $request, ReservationService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($request->user()->getId(), $data);

        return (new ReservationResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Reservation $reservation): ReservationResource
    {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return new ReservationResource($reservation->load(['ticketCategory.match']));
    }

    public function update(
        UpdateReservationRequest $request,
        Reservation $reservation,
        ReservationService $service
    ): ReservationResource {
        if ($reservation->user_id !== $request->user()->getId()) {
            abort(403);
        }

        $data = $request->validated();
        $item = $service->update($reservation, $data);

        return new ReservationResource($item);
    }

    public function destroy(Reservation $reservation, ReservationService $service): JsonResponse
    {
        $service->delete($reservation);

        return response()->json(null, 204);
    }
}
