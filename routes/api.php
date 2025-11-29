<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Notifications\NotificationController;
use App\Http\Controllers\Api\Notifications\NotificationSettingsController;

Route::group(['prefix'=> 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification_id}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        Route::get('/notification-settings', [NotificationSettingsController::class, 'index']);
        Route::put('/notification-settings', [NotificationSettingsController::class, 'update']);
    });
});
