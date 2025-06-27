<?php

namespace App\Models;

use App\Enum\AppointmentStatus as EnumAppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AppointmentStatus;

class Appointment extends Model
{
    protected $fillable = [
        'client_id', 'provider_id', 'service_id',
        'date', 'start_time', 'end_time', 'status',
    ];

    protected $casts = [
        'status' => EnumAppointmentStatus::class,
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
