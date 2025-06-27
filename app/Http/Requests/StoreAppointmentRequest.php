<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        // Tylko klient może tworzyć rezerwację
        return auth()->check() && auth()->user()->isClient();
    }

    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'provider_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ];
    }
}
