<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/fields', [FieldController::class, 'index']);
Route::get('/fields/{field}', [FieldController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin,superadmin'])->group(function () {
    Route::post('/fields', [FieldController::class, 'store']);
    Route::put('/fields/{field}', [FieldController::class, 'update']);
    Route::patch('/fields/{field}', [FieldController::class, 'update']);
    Route::delete('/fields/{field}', [FieldController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
});
