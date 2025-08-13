<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'language_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
