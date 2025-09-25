<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Service;
use App\Policies\ServicePolicy;
use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use App\Repositories\ChatRepository;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserServiceRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\PhotoRepository;
use App\Repositories\UserServiceRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChatRepositoryInterface::class, ChatRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceRepositoryInterface::class, UserServiceRepository::class);
        $this->app->bind(PhotoRepositoryInterface::class, PhotoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // JsonResource::withoutWrapping();
    }

    protected $policies = [
        Service::class => ServicePolicy::class,
        Appointment::class => AppointmentPolicy::class,
    ];
}
