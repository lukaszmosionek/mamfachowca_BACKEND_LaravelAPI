<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        // return true;
        // Tylko klient może tworzyć rezerwację
        return auth()->check() && auth()->user()->isClient();
    }

    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            // 'provider_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ];
    }

    public function validateAvailability($service)
    {
        $startTime = $this->start_time;
        $endTime = date('H:i', strtotime($startTime . " + {$service->duration_minutes} minutes"));

        $dayName = strtolower(date('l', strtotime($this->date)));

        foreach ($service->provider->availabilities as $a) {
            $isTimeOutside = $startTime < $a->start_time || $endTime > $a->end_time;

            if ($dayName == $a->day_of_week && $isTimeOutside) {
                throw ValidationException::withMessages([
                    'start_time' => 'Provider is not available these hours',
                ]);
            }
        }

        return $endTime;
    }
}
