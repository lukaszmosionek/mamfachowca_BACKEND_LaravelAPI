<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

// /log-viewer -> View LOG
// /telescope -> Laravel Telescope

Route::get('/', function () {
    return redirect('/docs/api');
    // echo '<a href="/docs/api">API DOCS</a>';
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

Route::middleware(['throttle:1,5']) // 1 request every 5 minutes
    ->get('/clear-cache', function (Request $request) {
        abort_unless($request->query('key') === env('CACHE_CLEAR_KEY'), 403, 'Unauthorized. Find clear-cache key in .env file.');

        Artisan::call('app:clear-cache');

        return response()->json([
            'status' => 'success',
            'message' => 'Cache cleared via custom command!',
            'timestamp' => now()->toDateTimeString(),
        ]);
    });

