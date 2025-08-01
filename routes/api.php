<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->name('register');;
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::apiResource('services', ServiceController::class)->only('index', 'show');
Route::apiResource('providers', ProviderController::class)->only('index');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('me/services', UserServiceController::class);

    Route::get('me', [ProfileController::class, 'getUser'])->name('profile.getUser');
    Route::put('me', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{item}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/{item}', [FavoriteController::class, 'isFavorited']);

    Route::apiResource('appointments', AppointmentController::class)->except(['update']);
    Route::post('appointments/{appointment}/accept', [AppointmentController::class, 'accept']);
    Route::post('appointments/{appointment}/decline', [AppointmentController::class, 'decline']);


    Route::get('fetchMessagedUsers', [MessageController::class, 'fetchMessagedUsers'])->name('fetchMessagedUsers');
    Route::apiResource('users.messages', MessageController::class)->only(['index', 'show', 'store']);
    Route::apiResource('users', UsersController::class)->only(['show']);

    Route::apiResource('notifications', NotificationController::class)->only(['index']);
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Route::get('/enums', function () {
//     return response()->json([
//         'appointment_statuses' => AppointmentStatus::cases(),
//         'roles' => Role::cases(),
//     ]);
// });





