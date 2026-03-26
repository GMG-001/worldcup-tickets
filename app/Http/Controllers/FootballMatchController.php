<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootballMatch\StoreFootballMatchRequest;
use App\Http\Requests\FootballMatch\UpdateFootballMatchRequest;
use App\Http\Resources\FootballMatchResource;
use App\Models\FootballMatch;
use App\Services\FootballMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FootballMatchController extends Controller
{
    public function index(FootballMatchService $service): AnonymousResourceCollection
    {
        return FootballMatchResource::collection($service->getAll());
    }

    public function store(StoreFootballMatchRequest $request, FootballMatchService $service): JsonResponse
    {
        $data = $request->validated();
        $item = $service->create($data);

        return (new FootballMatchResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function show(FootballMatch $footballMatch): FootballMatchResource
    {
        return new FootballMatchResource($footballMatch->load('ticketCategories'));
    }

    public function update(
        UpdateFootballMatchRequest $request,
        FootballMatch $footballMatch,
        FootballMatchService $service
    ): FootballMatchResource {

        $data = $request->validated();
        $item = $service->update($footballMatch, $data);

        return new FootballMatchResource($item);
    }

    public function destroy(FootballMatch $footballMatch, FootballMatchService $service): JsonResponse
    {
        $service->delete($footballMatch);

        return response()->json(null, 204);
    }

    public function report(): void
    {
        //
    }
}
