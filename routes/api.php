<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FootballMatchController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware(['auth:sanctum', 'is-fan'])->group(function () {
    Route::prefix('tickets') -> group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::get('{id}', [TicketController::class, 'store']);
    });

    Route::prefix('reservations')->group(function () {
        Route::post('/', [ReservationController::class, 'index']);
        Route::post('{id}/pay', [ReservationController::class, 'store']);
        Route::delete('{id}', [ReservationController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum', 'is-admin'])->prefix('admin')->group(function () {
    Route::prefix('matches')->group(function () {
        Route::post('/', [FootballMatchController::class, 'store']);
        Route::get('{id}/report', [FootballMatchController::class, 'report']);
    });
});

Route::prefix('matches')->group(function () {
    Route::post('/', [FootballMatchController::class, 'index']);
    Route::get('{id}', [FootballMatchController::class, 'show']);
});

