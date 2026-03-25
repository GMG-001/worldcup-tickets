<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCategory\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategory\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use Illuminate\Http\JsonResponse;

class TicketCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TicketCategory::with('match')->get());
    }

    public function store(StoreTicketCategoryRequest $request): JsonResponse
    {
        $category = TicketCategory::create($request->validated());

        return response()->json($category, 201);
    }

    public function show(TicketCategory $ticketCategory): JsonResponse
    {
        return response()->json($ticketCategory->load('match'));
    }

    public function update(UpdateTicketCategoryRequest $request, TicketCategory $ticketCategory): JsonResponse
    {
        $ticketCategory->update($request->validated());

        return response()->json($ticketCategory);
    }

    public function destroy(TicketCategory $ticketCategory): JsonResponse
    {
        $ticketCategory->delete();

        return response()->json(null, 204);
    }
}
