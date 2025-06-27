<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Klient widzi swoje rezerwacje
    public function index()
    {
        $appointments = auth()->user()->appointmentsAsClient()->with('service', 'provider')->get();
        return response()->json($appointments);
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

        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return response()->json($appointment->load('service', 'provider', 'client'));
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return response()->json(null, 204);
    }
}
