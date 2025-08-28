<?php

namespace App\Http\Controllers;

use App\Enum\AppointmentStatus;
use App\Enum\Role;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Notifications\NewAppointmentNotification;
use App\Notifications\NewMessageNotification;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // Klient widzi swoje rezerwacje
    public function index()
    {
        $perPage = request('per_page', 10);

        if( auth()->user()->role == Role::CLIENT ){
            $appointments = auth()->user()->appointmentsAsClient()->with('service', 'provider')->latest()->paginate($perPage);
        }elseif( auth()->user()->role == Role::PROVIDER OR auth()->user()->role == Role::ADMIN ){
            $appointments = auth()->user()->appointmentsAsProvider()->with('service', 'client:id,name,role')->latest()->paginate($perPage);
        }

        return $this->success([
            'appointments' => AppointmentResource::collection($appointments),
            'last_page' => $appointments->lastPage()

        ], 'Appointments retrieved successfully');
    }

    public function store(StoreAppointmentRequest $request)
    {
        $service = Service::with(['provider:id,name','provider.availabilities'])->findOrFail($request->service_id);

        $endTime = $endTime = date('H:i', strtotime(date('Y-m-d') . " {$request->start_time} + {$service->duration_minutes} minutes"));

        // $endTime = $request->validateAvailability($service);

        $client = auth()->user();
        $provider = User::findorFail($service->provider->id);

        $appointment = Appointment::create([
            'client_id' => $client->id,
            'provider_id' => $service->provider->id,
            'service_id' => $service->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $endTime,
            'status' => AppointmentStatus::Pending,
        ]);

        $provider->notify(new NewAppointmentNotification($service->provider));

        $appointment = new AppointmentResource($appointment);
        return $this->success( compact('appointment'), 'Appointment stored successfully', 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $appointment = AppointmentResource::collection($appointment);
        return $this->success($appointment->load('service', 'provider', 'client'), 'Appointment fetched successfully');
    }

    public function handleAction(Appointment $appointment, string $action)
    {
        if ($action === 'accept') {
            $appointment->status = AppointmentStatus::Confirmed;
            $message = 'Appointment accepted successfully';
        } elseif ($action === 'decline') {
            $appointment->status = AppointmentStatus::Cancelled;
            $message = 'Appointment declined successfully';
        }else{
            abort(404, 'Action not found'); // or return an error response
        }

        $appointment->save();
        return $this->success(null, $message);

    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return $this->success(null, 'Appointment delete successfully', 204);
    }
}
