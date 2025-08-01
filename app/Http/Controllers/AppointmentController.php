<?php

namespace App\Http\Controllers;

use App\Enum\AppointmentStatus;
use App\Enum\Role;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // Klient widzi swoje rezerwacje
    public function index()
    {
        if( auth()->user()->role == Role::Client ){
            $appointments = auth()->user()->appointmentsAsClient()->with('service', 'provider')->latest()->get();
        }elseif( auth()->user()->role == Role::Provider ){
            $appointments = auth()->user()->appointmentsAsProvider()->with('service', 'client:id,name,role')->latest()->get();
        }

        $appointments = AppointmentResource::collection($appointments);
        return $this->success($appointments, 'Appointments retrieved successfully');
    }

    public function store(StoreAppointmentRequest $request)
    {
        $service = Service::with(['provider:id,name','provider.availabilities'])->findOrFail($request->service_id);

        $endTime = $request->validateAvailability($service);

        $appointment = Appointment::create([
            'client_id' => auth()->id(),
            'provider_id' => $service->provider->id,
            'service_id' => $service->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $endTime,
            'status' => AppointmentStatus::Pending,
        ]);

        $appointment = new AppointmentResource($appointment);
        return $this->success($appointment, 'Appointment stored successfully', 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $appointment = AppointmentResource::collection($appointment);
        return $this->success($appointment->load('service', 'provider', 'client'), 'Appointment fetched successfully');
    }

    public function accept(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);

        // if ($appointment->status !== AppointmentStatus::Pending) {
        //     return $this->error('Appointment cannot be accepted', 400);
        // }

        $appointment->status = AppointmentStatus::Confirmed;
        $appointment->save();

        return $this->success(new AppointmentResource($appointment->load('service', 'provider', 'client')), 'Appointment accepted successfully');
    }

    public function decline(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);

        // if ($appointment->status !== AppointmentStatus::Pending) {
        //     return $this->error('Appointment cannot be accepted', 400);
        // }

        $appointment->status = AppointmentStatus::Cancelled;
        $appointment->save();

        return $this->success(new AppointmentResource($appointment->load('service', 'provider', 'client')), 'Appointment declined successfully');
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return $this->success(null, 'Appointment delete successfully', 204);
    }
}
