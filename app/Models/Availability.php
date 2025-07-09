<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use  HasFactory;

    protected $fillable = [
        'provider_id', 'day_of_week', 'start_time', 'end_time',
    ];

    // provider
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
