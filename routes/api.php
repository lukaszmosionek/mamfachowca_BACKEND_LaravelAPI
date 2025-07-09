<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');;
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');;

Route::get('services/all', [ServiceController::class, 'all'])->name('services.all');
Route::get('providers/all', [ProviderController::class, 'index'])->name('providers.all');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('appointments', AppointmentController::class)->except(['update']);

    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user', [UserController::class, 'update']);
});





