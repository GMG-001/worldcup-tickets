<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request, TicketService $service): JsonResponse
    {
        return response()->json($service->getByUser($request->user()->getId()));
    }

    public function store(StoreTicketRequest $request, TicketService $service): JsonResponse
    {
        return response()->json(
            $service->create($request->user()->getId(), $request->validated()),
            201,
        );
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->getId()) {
            abort(403);
        }

        return response()->json($ticket->load(['match', 'category']));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, TicketService $service): JsonResponse
    {
        return response()->json($service->update($ticket, $request->validated()));
    }

    public function destroy(Ticket $ticket, TicketService $service): JsonResponse
    {
        $service->delete($ticket);

        return response()->json(null, 204);
    }
}