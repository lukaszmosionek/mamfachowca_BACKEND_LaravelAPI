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
    // public function store(Request $request)
    {

        // return($request->all());

        $service = \App\Models\Service::findOrFail($request->service_id);

        $appointment = Appointment::create([
            'client_id' => auth()->id(),
            'provider_id' => $service->provider->id,
            'service_id' => $service->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => date('H:i', strtotime($request->start_time . " + {$service->duration_minutes} minutes")),
            'status' => 'pending',
        ]);

        // $appointment = AppointmentResource::collection($appointment);
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
