<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=> 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
});

use App\Http\Controllers\Api\Notifications\NotificationController;
use App\Http\Controllers\Api\Notifications\NotificationSettingsController;

Route::middleware('auth:sanctum')->group(function () {

    // Notifications list & counts
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Mark read
    Route::patch('/notifications/{user_notification_id}/read', [NotificationController::class, 'markRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Delete
    Route::delete('/notifications/{user_notification_id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

    // Settings
    Route::get('/notification-settings', [NotificationSettingsController::class, 'show']);
    Route::put('/notification-settings', [NotificationSettingsController::class, 'update']);
});
