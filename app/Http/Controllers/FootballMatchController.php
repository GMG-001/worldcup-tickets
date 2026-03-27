<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootballMatch\StoreFootballMatchRequest;
use App\Http\Requests\FootballMatch\UpdateFootballMatchRequest;
use App\Http\Resources\FootballMatchResource;
use App\Models\FootballMatch;
use App\Services\FootballMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class FootballMatchController extends Controller
{
    public function index(FootballMatchService $service): AnonymousResourceCollection
    {
        $items = $service->index();

        return FootballMatchResource::collection($items);
    }

    public function show(int $id, FootballMatchService $service): FootballMatchResource
    {
        $item = $service->findOrFail($id);

        return new FootballMatchResource($item);
    }

    public function create(StoreFootballMatchRequest $request, FootballMatchService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($data);

        return (new FootballMatchResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        int $id,
        UpdateFootballMatchRequest $request,
        FootballMatchService $service
    ): FootballMatchResource {
        $data = $request->validated();

        $footballMatch = $service->findOrFail($id);
        $item = $service->update($footballMatch, $data);

        return new FootballMatchResource($item);
    }

    public function report(int $id, FootballMatchService $service): JsonResponse
    {
        $item = $service->findOrFail($id);
        $data = $service->report($item);

        return response()->json([
            'match'         => new FootballMatchResource($data['match']),
            'total_revenue' => $data['total_revenue'],
            'categories'    => $data['categories']->map(fn ($cat) => [
                'name'            => $cat->getName(),
                'price'           => $cat->getPrice(),
                'seat_count'      => $cat->getSeatCount(),
                'available_count' => $cat->getAvailableCount(),
                'tickets_sold'    => $cat->tickets_sold,
                'revenue'         => $cat->revenue,
            ]),
        ]);
    }
}
