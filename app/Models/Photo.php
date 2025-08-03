<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Photo extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['thumbnail', 'medium', 'large', 'is_main'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
