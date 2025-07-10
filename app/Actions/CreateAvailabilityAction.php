<?php

namespace App\Actions;

use App\Models\Availability;
use App\Models\User;

class CreateAvailabilityAction
{
    public function execute(User $user, array $availabilityData): void
    {
        $availabilities = [];

        foreach ($availabilityData['start'] as $day => $start) {
            $end = $availabilityData['end'][$day];

            if( !($start AND $end) ) continue;

            $availabilities[] = [
                'provider_id' => $user->id,
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        Availability::insert($availabilities);
    }
}
