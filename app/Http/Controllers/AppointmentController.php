<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // Klient widzi swoje rezerwacje
    public function index()
    {
        $appointments = auth()->user()->appointmentsAsClient()->with('service', 'provider')->get();
        $appointments = AppointmentResource::collection($appointments);
        return $this->success($appointments, 'Appointments retrieved successfully');
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        $service = \App\Models\Service::findOrFail($data['service_id']);

        $appointment = Appointment::create([
            'client_id' => auth()->id(),
            'provider_id' => $data['provider_id'],
            'service_id' => $service->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => date('H:i', strtotime($data['start_time'] . " + {$service->duration_minutes} minutes")),
            'status' => 'pending',
        ]);

        $appointment = AppointmentResource::collection($appointment);
        return $this->success($appointment, 'Appointment stored successfully', 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $appointment = AppointmentResource::collection($appointment);
        return $this->success($appointment->load('service', 'provider', 'client'), 'Single appointment fetched successfully');
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return $this->success(null, 'Appointment delete successfully', 204);
    }
}
