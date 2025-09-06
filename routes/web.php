<?php

use App\Models\User;
use App\Notifications\NewNotification;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// /log-viewer -> View LOG

Route::get('/', function () {
    echo '<a href="/docs/api">API DOCS</a>';
});

Route::get('/migrate', function () {
    if (app()->environment('production')) {
        abort(403, "Forbidden in production!");
    }

    Artisan::call('migrate:fresh', [
        '--seed' => true, // optional, if you want to run seeders too
    ]);

    return "Database migrated fresh!";
});

