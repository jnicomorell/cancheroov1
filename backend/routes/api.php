<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/fields', [FieldController::class, 'index']);
Route::post('/fields', [FieldController::class, 'store'])->middleware('auth:sanctum');
Route::get('/fields/{field}', [FieldController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
});
