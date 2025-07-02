<?php

use Illuminate\Support\Facades\Route;

Route::get('/deploy', function () {
        // Path to the shell script
        $scriptPath = storage_path('deploy.sh');
        dump($scriptPath);
        // $scriptPath = 'deploy.sh';

        // Execute the script
        $output = shell_exec("bash $scriptPath 2>&1");

        // Log the output or do something with it
        \Log::info($output);

        dump($output);

        // Return a response to the user
        return response("Script executed successfully!");
});
