<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentActionService;
use App\Services\AppointmentService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppointmentController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index(AppointmentService $appointmentService)
    {
        $perPage = request('per_page', 10);
        $appointments = $appointmentService->getAppointmentsForUser(auth()->user(), $perPage);

        return $this->success([
            'appointments' => AppointmentResource::collection($appointments),
            'last_page' => $appointments->lastPage()
        ], 'Appointments retrieved successfully');
    }

    public function store(StoreAppointmentRequest $request, AppointmentService $appointmentService)
    {
        $appointment = $appointmentService->book(
            auth()->user(),
            $request->service_id,
            $request->date,
            $request->start_time
        );

        return $this->success([
            'appointment' => new AppointmentResource($appointment)
        ], 'Appointment stored successfully', 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        $appointment->load(['service', 'provider', 'client']);

        return $this->success([
            'appointment' => new AppointmentResource($appointment)
        ], 'Appointment fetched successfully');
    }

    public function handleAction(Appointment $appointment, string $action, AppointmentActionService $service)
    {
        try {
            $message = $service->handle($appointment, $action);
            return $this->success(null, $message);
        } catch (\InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        abort_if( auth()->id() !== $appointment->client_id, 403, 'You can only delete your own appointments.');
        $appointment->delete();
        return $this->success(null, 'Appointment delete successfully', 204);
    }
}
