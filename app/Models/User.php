<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'role',
        'password',
        'remember_token',
        'lang',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'role' => Role::class,
        'password' => 'hashed',
        'remember_token' => 'string',
        'lang' => 'string',
        'avatar' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function isProvider(): bool
    {
        return $this->role === Role::PROVIDER;
    }

    public function isClient(): bool
    {
        return $this->role === Role::CLIENT;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Others
    |--------------------------------------------------------------------------
    */
    public static function storeAvatarFile($file): string
    {
        $folder = 'avatars/'.now()->format('o-\WW');
        Storage::disk('public')->makeDirectory($folder);
        $path = Storage::disk('public')->putFile($folder, $file);

        if( !config('app.is_symlinks_working') ){
            // Full source and destination paths
            $source = storage_path('app/public/' . $path);
            $destination = public_path('storage/' . $path);

            // Ensure destination directory exists
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }

            // Copy file to public/storage
            copy($source, $destination);
        }

        return $path;
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
