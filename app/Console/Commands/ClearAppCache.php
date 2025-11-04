<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ClearAppCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You can run it like: php artisan app:clear-cache
     */
    protected $signature = 'app:clear-cache';

    /**
     * The console command description.
     */
    protected $description = 'Clears all Laravel caches (config, route, view, etc.)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Clearing application cache...');

        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('route:clear');
        Artisan::call('route:cache');
        Artisan::call('optimize:clear');

        // (Optional) Log who/when cleared cache
        Log::info('Application cache cleared manually via Artisan command.');

        $this->info('✅ All caches cleared successfully!');
    }
}
