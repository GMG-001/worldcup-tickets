<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCategory\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategory\UpdateTicketCategoryRequest;
use App\Http\Resources\TicketCategoryResource;
use App\Models\TicketCategory;
use App\Services\TicketCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketCategoryController extends Controller
{
    public function index(TicketCategoryService $service): AnonymousResourceCollection
    {
        return TicketCategoryResource::collection($service->getAll());
    }

    public function store(StoreTicketCategoryRequest $request, TicketCategoryService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($data);

        return (new TicketCategoryResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function show(TicketCategory $ticketCategory): TicketCategoryResource
    {
        return new TicketCategoryResource($ticketCategory->load('match'));
    }

    public function update(
        UpdateTicketCategoryRequest $request,
        TicketCategory $ticketCategory,
        TicketCategoryService $service
    ): TicketCategoryResource {
        $data = $request->validated();
        $item = $service->update($ticketCategory, $data);

        return new TicketCategoryResource($item);
    }

    public function destroy(TicketCategory $ticketCategory, TicketCategoryService $service): JsonResponse
    {
        $service->delete($ticketCategory);

        return response()->json(null, 204);
    }
}
