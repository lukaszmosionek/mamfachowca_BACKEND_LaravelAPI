<?php

namespace App\Models;

use App\Enum\AppointmentStatus as EnumAppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'client_id',
        'provider_id',
        'service_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'client_id' => 'integer',
        'provider_id' => 'integer',
        'service_id' => 'integer',
        'date' => 'date',
        'start_time' => 'datetime:H:i:s', // stored as TIME
        'end_time' => 'datetime:H:i:s',   // stored as TIME
        'status' => EnumAppointmentStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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
