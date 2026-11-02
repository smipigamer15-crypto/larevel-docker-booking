<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/token', [AuthController::class, 'token'])
        ->middleware('throttle:api-token')
        ->name('api.auth.token');
});

Route::prefix('v1')->middleware(['auth:sanctum', 'api.logger', 'throttle:api'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me'])
        ->name('api.auth.me');
    Route::delete('/auth/token', [AuthController::class, 'revoke'])
        ->name('api.auth.revoke');

    Route::get('/profile', [ProfileController::class, 'show'])
        ->middleware('abilities:profile:read')
        ->name('api.profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->middleware('abilities:profile:write')
        ->name('api.profile.update');

    Route::get('/services', [ServiceController::class, 'index'])
        ->middleware('abilities:services:read')
        ->name('api.services.index');
    Route::get('/services/{id}', [ServiceController::class, 'show'])
        ->middleware('abilities:services:read')
        ->name('api.services.show');

    Route::get('/bookings', [BookingController::class, 'index'])
        ->middleware('abilities:bookings:read')
        ->name('api.bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])
        ->middleware('abilities:bookings:read')
        ->name('api.bookings.show');
    Route::post('/bookings', [BookingController::class, 'store'])
        ->middleware('abilities:bookings:write')
        ->name('api.bookings.store');
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel'])
        ->middleware('abilities:bookings:write')
        ->name('api.bookings.cancel');
});
