<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Photo extends Authenticatable
{
    use HasFactory;

    protected $table = 'photos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'original',
        'thumbnail',
        'medium',
        'large',
        'is_main',
        'original_filename',
    ];

    protected $casts = [
        'id' => 'integer',
        'imageable_id' => 'integer',
        'imageable_type' => 'string',
        'original' => 'string',
        'thumbnail' => 'string',
        'medium' => 'string',
        'large' => 'string',
        'is_main' => 'boolean',
        'original_filename' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function imageable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Static methods
    |--------------------------------------------------------------------------
    */
    public static array $uploadLimits = [
        'max_size' => 10 * 1024 * 1024, // 10MB
    ];

    public static array $sizes = [
        'thumbnail' => ['width' => 150, 'height' => 150],
        'medium'    => ['width' => 300, 'height' => 300],
        'large'     => ['width' => 800, 'height' => 600],
    ];
    public static function getSizes(): array
    {
        return self::$sizes;
    }
    public static function getSize(string $size): ?array
    {
        return self::$sizes[$size] ?? null;
    }
    public static function getSizeKeys(): array
    {
        return array_keys(self::$sizes);
    }
    public static function storeFile($file): string
    {
        $folder = 'photos/' . now()->format('o-\WW');
        Storage::disk('public')->makeDirectory($folder);

        // Store file in storage/app/public/photos/...
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk('public')->putFileAs($folder, $file, $fileName);

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
    public static function getUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}
