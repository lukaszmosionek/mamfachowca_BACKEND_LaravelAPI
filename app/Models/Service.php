<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use  HasFactory;

    protected $fillable = [
        'provider_id', 'name', 'description', 'price', 'duration_minutes',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }
    public function favoritedBy() {
        return $this->belongsToMany(User::class, 'favorites', 'service_id', 'user_id');
    }
}
