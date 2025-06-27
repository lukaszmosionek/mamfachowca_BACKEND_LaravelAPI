<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->client_id || $user->id === $appointment->provider_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        // Np. klient może odwołać tylko swoją rezerwację
        return $user->id === $appointment->client_id;
    }
}
