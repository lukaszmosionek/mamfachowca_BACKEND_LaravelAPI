<?php

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo '<a href="/docs/api">API DOCS</a>';
});

Route::get('/deploy', function () {

        $gitOutput = shell_exec('git pull 2>&1');
        // $composerOutput = shell_exec('cd .. && php82-cli composer install 2>&1');
        $composerOutput = shell_exec('cd ../ && composer install 2>&1');
        Artisan::call('migrate:fresh', [
            '--seed' => true,
        ]);

        echo '<h2>Git output</h2>';
        dump($gitOutput);

        echo '<h2>Composer Output</h2>';
        dump($composerOutput);

        echo '<h2>Migrate Output</h2>';
        dump(Artisan::output());

});
