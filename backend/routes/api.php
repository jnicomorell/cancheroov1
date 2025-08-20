<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReviewController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/fields', [FieldController::class, 'index']);
Route::get('/fields/map', [FieldController::class, 'map']);
Route::post('/fields', [FieldController::class, 'store'])->middleware('auth:sanctum');
Route::get('/fields/{field}', [FieldController::class, 'show']);
Route::put('/fields/{field}', [FieldController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::get('/reservations/{reservation}/ics', [ReservationController::class, 'ics']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
    Route::post('/reservations/{reservation}/pay', [ReservationController::class, 'pay']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});
