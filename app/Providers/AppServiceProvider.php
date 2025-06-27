<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Service;
use App\Policies\ServicePolicy;
use App\Models\Appointment;
use App\Policies\AppointmentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected $policies = [
        Service::class => ServicePolicy::class,
        Appointment::class => AppointmentPolicy::class,
    ];
}
