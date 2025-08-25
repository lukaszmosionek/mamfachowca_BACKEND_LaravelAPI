<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Photo extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['original_filename','original', 'thumbnail', 'medium', 'large', 'is_main'];

    //relations
    public function imageable()
    {
        return $this->morphTo();
    }

    public static array $uploadLimits = [
        'max_size' => 10 * 1024 * 1024, // 10MB
    ];

    public static array $sizes = [
        'thumbnail' => ['width' => 150, 'height' => 150], //['width' => 150, 'height' => 150],
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
        $folder = 'photos/'.now()->format('o-\WW');
        Storage::disk('public')->makeDirectory($folder);
        return Storage::disk('public')->putFile($folder, $file);
    }
    public static function getUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}
