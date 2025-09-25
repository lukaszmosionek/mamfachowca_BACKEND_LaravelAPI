<?php

namespace App\Services;

use App\Enum\AppointmentStatus;
use App\Enum\Role;
use App\Models\{Appointment, Service, User};
use App\Notifications\NewAppointmentNotification;
use Carbon\Carbon;

class AppointmentService
{
    public function getAppointmentsForUser(User $user, int $perPage = 10)
    {
        $query = match($user->role) {
            Role::CLIENT => $user->appointmentsAsClient()->with('service', 'provider'),
            Role::PROVIDER, Role::ADMIN => $user->appointmentsAsProvider()->with('service', 'client:id,name,role'),
            default => throw new \Exception('Unauthorized')
        };

        return $query->latest()->paginate($perPage);
    }

    public function book(User $client, int $serviceId, string $date, string $startTime): Appointment
    {
        $service = Service::with(['provider:id,name','provider.availabilities'])
            ->findOrFail($serviceId);

        $provider = $service->provider;

        // Calculate end time with Carbon
        $endTime = Carbon::parse("{$date} {$startTime}")
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

        // Create appointment
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => AppointmentStatus::Pending,
        ]);

        // Notify provider
        $provider->notify(new NewAppointmentNotification($provider));

        return $appointment;
    }
}
