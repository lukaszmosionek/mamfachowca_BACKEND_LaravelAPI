<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->name('register');;
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout'])->name('logout');;

Route::get('services', [ServiceController::class, 'index'])->name('services.index');
Route::get('providers', [ProviderController::class, 'index'])->name('providers.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('me/services', UserServiceController::class);
    Route::apiResource('appointments', AppointmentController::class)->except(['update']);

    Route::get('me', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('me', [ProfileController::class, 'update'])->name('profile.update');

    Route::apiResource('chats', ChatController::class)->only(['index', 'show', 'store']);

    Route::apiResource('notifications', NotificationController::class)->only(['index']);
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);





