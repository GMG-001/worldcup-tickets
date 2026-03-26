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
        return TicketResource::collection($service->getByUser($request->user()->getId()));
    }

    public function store(StoreTicketRequest $request, TicketService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($request->user()->getId(), $data);

        return (new TicketResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Ticket $ticket): TicketResource
    {
        if ($ticket->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return new TicketResource($ticket->load(['match', 'category']));
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
