<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request, TicketService $service): AnonymousResourceCollection
    {
        $authUser = $request->user();
        $items = $service->getByUser($authUser->getId());

        return TicketResource::collection($items);
    }

    public function show(Ticket $ticket, TicketService $service): TicketResource
    {
        $this->authorize('view', $ticket);

        return new TicketResource($service->show($ticket->getId()));
    }

    public function store(StoreTicketRequest $request, TicketService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($request->user()->getId(), $data);

        return (new TicketResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, TicketService $service): TicketResource
    {
        $data = $request->validated();
        $item = $service->update($ticket, $data);

        return new TicketResource($item);
    }

    public function destroy(Ticket $ticket, TicketService $service): JsonResponse
    {
        $service->delete($ticket);

        return response()->json(null, 204);
    }
}
