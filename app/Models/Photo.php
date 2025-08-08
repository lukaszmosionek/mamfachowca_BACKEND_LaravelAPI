<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Photo extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['thumbnail', 'medium', 'large', 'is_main'];

    //relations
    public function imageable()
    {
        return $this->morphTo();
    }

    public static array $uploadLimits = [
        'max_size' => 10 * 1024 * 1024, // 10MB
    ];

    public static array $sizes = [
        'thumbnail' => [150, 150], //['width' => 150, 'height' => 150],
        'medium'    => [300, 300],
        'large'     => [800, 600],
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
}
