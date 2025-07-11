<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Availability extends Model
{
    use  HasFactory;

    protected $fillable = [
        'provider_id', 'day_of_week', 'start_time', 'end_time',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i'),
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i'),
        );
    }

    // provider
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
