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

    //relations
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
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'service_id', 'user_id');
    }
    public function favoritedByUser($userId)
    {
        return $this->favoritedBy()->where('user_id', $userId);
    }
    // END relations

    public function scopeFilter($query)
    {
            $search = request('name');
            $provider_id = request('provider_id');

            if( $provider_id OR $search ) request()->merge(['page' => 1]); // Force page 1

            return $query
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($provider_id, function ($q, $provider_id) {
                $q->where('provider_id', $provider_id);
            });

    //     return $query
    //         ->when($filters['title'] ?? false, fn($q, $title) => $q->where('title', 'like', "%$title%"))
    //         ->when($filters['status'] ?? false, fn($q, $status) => $q->where('status', $status))
    //         ->when($filters['author_id'] ?? false, fn($q, $author_id) => $q->where('author_id', $author_id));
    }
}
