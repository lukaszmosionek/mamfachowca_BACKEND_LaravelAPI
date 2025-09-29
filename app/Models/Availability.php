<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Availability extends Model
{
    use  HasFactory;

    protected $table = 'availabilities';

    protected $fillable = [
        'provider_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public const DAYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    protected $casts = [
        'id' => 'integer',
        'provider_id' => 'integer',
        'day_of_week' => 'string',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getStartTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i:s');
    }

    public function getEndTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i:s');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
