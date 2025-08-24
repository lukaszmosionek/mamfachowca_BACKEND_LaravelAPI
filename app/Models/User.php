<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'lang',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function isProvider(): bool
    {
        return $this->role === Role::Provider;
    }

    public function isClient(): bool
    {
        return $this->role === Role::Client;
    }

        // === Relations ===

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'provider_id');
    }

    public function appointmentsAsClient()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function appointmentsAsProvider()
    {
        return $this->hasMany(Appointment::class, 'provider_id');
    }
    public function favorites()
    {
        return $this->belongsToMany(Service::class, 'favorites');
    }

    //store avatar path
    public static function storeAvatarFile($file): string
    {
        return Storage::disk('public')->putFile('avatars/'.now()->format('o-\WW'), $file);
    }
    public static function getAvatarUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
    public static function deleteAvatarFile(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
