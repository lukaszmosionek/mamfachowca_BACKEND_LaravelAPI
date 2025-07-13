<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Get all available locales from the resources/lang directory
        $availableLocales = collect(File::directories(lang_path()))
            ->map(function ($path) {
                return basename($path);
            })->toArray();

        // Get the requested locale from the Accept-Language header
        $locale = $request->header('Accept-Language', Config::get('app.locale'));
        $locale = substr($locale, 0, 2); // Get first two characters (e.g. 'en' from 'en-US')

        // Use requested locale if available, otherwise fallback to config('app.locale')
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        } else {
            App::setLocale(Config::get('app.locale'));
        }

        return $next($request);
    }
}
