<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

// Run it from CLI:
// php artisan db:fresh-storage

class FreshMigrateAndStorage extends Command
{
    protected $signature = 'db:fresh-storage';
    protected $description = 'Run migrate:fresh and reset storage folders';

    public function handle()
    {
        $this->info("Running migrate:fresh...");
        Artisan::call('migrate:fresh', ['--seed' => true]);
        $this->info("Migrations complete.");

        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');

        $this->info("Resetting storage folders...");
        File::deleteDirectory($publicPath);
        File::makeDirectory($publicPath.'/photos', 0755, true);
        File::makeDirectory($publicPath.'/avatars', 0755, true);

        File::copyDirectory($storagePath.'/photos', $publicPath.'/photos');
        File::copyDirectory($storagePath.'/avatars', $publicPath.'/avatars');

        $this->info("Storage reset complete.");
    }
}
