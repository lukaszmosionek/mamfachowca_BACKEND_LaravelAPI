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

    abort_if(app()->environment('production'), 403, 'Forbidden in production!');
    abort_if(request('key') !== env('MIGRATE_KEY'), 403, 'Invalid key');

    Artisan::queue('migrate:fresh', [
        '--seed' => true, // optional, if you want to run seeders too
    ]);

    return "Migrated!";
});

