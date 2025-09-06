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
    abort_if(request('key') !== config('app.migrate_key'), 403, 'Invalid key');

    // Remove time limit for web request
    set_time_limit(0);

    // Run the custom Artisan command
    Artisan::call('db:fresh-storage');

    return "Database migrated & storage reset!";
});

