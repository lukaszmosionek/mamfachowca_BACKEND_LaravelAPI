<?php

use App\Enum\AppointmentStatus;
use App\Enum\Role;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserServiceController;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');;
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
// });

Route::apiResource('services', ServiceController::class)->only('index', 'show');
Route::apiResource('providers', ProviderController::class)->only('index');
Route::apiResource('users', UsersController::class)->only(['show']);

Route::post('contact', [ContactController::class, 'send']);
Broadcast::routes(['middleware' => ['auth:sanctum']]);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::prefix('me')->group(function () {
        Route::get('/', [ProfileController::class, 'getUser'])->name('profile.getUser');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('avatar', [ProfileController::class, 'uploadAvatar']);

        // User Services
        Route::apiResource('services', UserServiceController::class)->names('me.services');
        Route::post('services/{service}/photos', [UserServiceController::class, 'storePhotos']);
        Route::delete('services/photos/{id}', [UserServiceController::class, 'destroyPhoto']);
    });

    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites/{item}/toggle', [FavoriteController::class, 'toggle']);
    Route::get('favorites/{item}', [FavoriteController::class, 'isFavorited']);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class)->except(['update']);
    Route::post('appointments/{appointment}/{action}', [AppointmentController::class, 'handleAction'])->where('action', 'accept|decline');

    // Messaging
    Route::get('messaged-users', [MessageController::class, 'fetchMessagedUsers'])->name('fetchMessagedUsers');
    Route::apiResource('users.messages', MessageController::class)->only(['index', 'show', 'store']);

    // Notifications
    Route::apiResource('notifications', NotificationController::class)->only(['index']);
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware([IsAdminMiddleware::class])->group(function () {
        Route::apiResource('users', AdminUsersController::class)->only(['index', 'destroy']);
        Route::apiResource('services', AdminServiceController::class)->only(['index', 'destroy']);
    });

});


/*
|--------------------------------------------------------------------------
| Test & Fallback
|--------------------------------------------------------------------------
*/
Route::get('test-api', function(){
    return response()->json([
        'message' => 'Connected to API!',
        // 'routes' => app(\App\Services\RouteService::class)->getAllRoutes(),
        // 'APPOINTMENT_STATUSES' => AppointmentStatus::cases(),
        // 'ROLES' => Role::cases(),
    ]);
});

//API_ROUTE_NOT_FOUND
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => '[404] API route not found: ' . request()->path().'.',
        'error_code' => 'API_ROUTE_NOT_FOUND'
    ], 404);
});



