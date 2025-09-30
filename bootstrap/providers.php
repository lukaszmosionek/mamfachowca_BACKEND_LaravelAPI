<?php

$providers[] = App\Providers\AppServiceProvider::class;
$providers[] = App\Providers\BroadcastServiceProvider::class;

// Only load Telescope outside production
if (!app()->environment('production')) {
    $providers[] = App\Providers\TelescopeServiceProvider::class;
}

return $providers;
