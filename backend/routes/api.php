<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ChatController, FieldController, LoyaltyController, MessageController, PromotionController, ReservationController, ReviewController, SocialAuthController, UserController};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::get('/fields', [FieldController::class, 'index']);
Route::get('/fields/map', [FieldController::class, 'map']);
Route::post('/fields', [FieldController::class, 'store'])->middleware(['auth:sanctum', 'role:admin,superadmin']);
Route::put('/fields/{field}', [FieldController::class, 'update'])->middleware(['auth:sanctum', 'role:admin,superadmin']);
Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->middleware(['auth:sanctum', 'role:admin,superadmin']);
Route::get('/fields/{field}', [FieldController::class, 'show']);

Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);
Route::get('/promotions', [PromotionController::class, 'index']);

Route::get('/chats', [ChatController::class, 'index']);
Route::get('/chats/{chat}', [ChatController::class, 'show']);
Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);

Route::get('/ranking', [UserController::class, 'ranking']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware('role:cliente,admin,superadmin');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::get('/reservations/{reservation}/ics', [ReservationController::class, 'ics']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->middleware('role:cliente,admin,superadmin');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->middleware('role:cliente,admin,superadmin');
    Route::post('/reservations/{reservation}/pay', [ReservationController::class, 'pay'])->middleware('role:cliente,admin,superadmin');
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    Route::get('/loyalty/balance', [LoyaltyController::class, 'balance']);
    Route::post('/loyalty/redeem', [LoyaltyController::class, 'redeem']);
});
