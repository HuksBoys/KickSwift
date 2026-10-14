<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API Endpoints
Route::get('/fields', [FieldController::class, 'apiIndex']);
Route::get('/fields/{slug}/slots', [FieldController::class, 'apiSlots']);
Route::post('/ai/chat', [AiChatController::class, 'chat']);
Route::post('/bookings/calculate-price', [BookingController::class, 'apiCalculatePrice']);

// Protected Booking & User Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'apiStore']);
    Route::get('/user/bookings', [BookingController::class, 'apiHistory']);
});
