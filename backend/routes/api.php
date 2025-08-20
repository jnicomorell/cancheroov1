<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\ReservationController;

Route::get('/fields', [FieldController::class, 'index']);
Route::post('/fields', [FieldController::class, 'store']);
Route::get('/fields/{field}', [FieldController::class, 'show']);

Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
