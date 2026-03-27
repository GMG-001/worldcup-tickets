<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Services\TicketService;
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

    public function show(int $id, TicketService $service): TicketResource
    {
        return new TicketResource($service->show($id));
    }
}
