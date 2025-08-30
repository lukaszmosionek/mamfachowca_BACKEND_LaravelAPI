<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class RouteService
{
    public function getAllRoutes()
    {
        return collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'methods' => $route->methods(),
                'action' => $route->getActionName(),
            ];
        });
    }
}
