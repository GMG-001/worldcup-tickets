<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCategory\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategory\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use App\Services\TicketCategoryService;
use Illuminate\Http\JsonResponse;

class TicketCategoryController extends Controller
{
    public function index(TicketCategoryService $service): JsonResponse
    {
        return response()->json($service->getAll());
    }

    public function store(StoreTicketCategoryRequest $request, TicketCategoryService $service): JsonResponse
    {
        return response()->json($service->create($request->validated()), 201);
    }

    public function show(TicketCategory $ticketCategory): JsonResponse
    {
        return response()->json($ticketCategory->load('match'));
    }

    public function update(
        UpdateTicketCategoryRequest $request,
        TicketCategory $ticketCategory,
        TicketCategoryService $service
    ): JsonResponse {
        return response()->json($service->update($ticketCategory, $request->validated()));
    }

    public function destroy(TicketCategory $ticketCategory, TicketCategoryService $service): JsonResponse
    {
        $service->delete($ticketCategory);

        return response()->json(null, 204);
    }
}
