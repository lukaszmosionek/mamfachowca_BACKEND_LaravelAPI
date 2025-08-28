<?php

namespace App\Policies;

use App\Enum\Role;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function view(User $user, Service $service): bool
    {
        return $user->id === $service->provider_id;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->role === Role::ADMIN OR $user->id === $service->provider_id;
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->id === $service->provider_id;
    }
}
