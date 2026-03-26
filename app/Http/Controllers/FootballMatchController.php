<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootballMatch\StoreFootballMatchRequest;
use App\Http\Requests\FootballMatch\UpdateFootballMatchRequest;
use App\Models\FootballMatch;
use App\Services\FootballMatchService;
use Illuminate\Http\JsonResponse;

class FootballMatchController extends Controller
{
    public function index(FootballMatchService $service): JsonResponse
    {
        return response()->json($service->getAll());
    }

    public function store(StoreFootballMatchRequest $request, FootballMatchService $service): JsonResponse
    {
        return response()->json($service->create($request->validated()), 201);
    }

    public function show(FootballMatch $footballMatch): JsonResponse
    {
        return response()->json($footballMatch);
    }

    public function update(UpdateFootballMatchRequest $request, FootballMatch $footballMatch, FootballMatchService $service): JsonResponse
    {
        return response()->json($service->update($footballMatch, $request->validated()));
    }

    public function destroy(FootballMatch $footballMatch, FootballMatchService $service): JsonResponse
    {
        $service->delete($footballMatch);

        return response()->json(null, 204);
    }

    public function report(): void
    {

    }
}