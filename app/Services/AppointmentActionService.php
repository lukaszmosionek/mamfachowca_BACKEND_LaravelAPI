<?php

namespace App\Services;

use App\Models\Appointment;
use App\Enum\AppointmentStatus;
use InvalidArgumentException;

class AppointmentActionService
{
    public function handle(Appointment $appointment, string $action): string
    {
        match ($action) {
            'accept' => $appointment->status = AppointmentStatus::Confirmed,
            'decline' => $appointment->status = AppointmentStatus::Cancelled,
            default => throw new InvalidArgumentException("Invalid action: {$action}")
        };

        $appointment->save();

        return match ($action) {
            'accept' => 'Appointment accepted successfully',
            'decline' => 'Appointment declined successfully',
        };
    }
}
