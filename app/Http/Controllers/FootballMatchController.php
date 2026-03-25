<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootballMatch\StoreFootballMatchRequest;
use App\Http\Requests\FootballMatch\UpdateFootballMatchRequest;
use App\Models\FootballMatch;
use Illuminate\Http\JsonResponse;

class FootballMatchController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FootballMatch::all());
    }

    public function store(StoreFootballMatchRequest $request): JsonResponse
    {
        $match = FootballMatch::create($request->validated());

        return response()->json($match, 201);
    }

    public function show(FootballMatch $footballMatch): JsonResponse
    {
        return response()->json($footballMatch);
    }

    public function update(UpdateFootballMatchRequest $request, FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->update($request->validated());

        return response()->json($footballMatch);
    }

    public function destroy(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->delete();

        return response()->json(null, 204);
    }
}
