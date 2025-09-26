<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies'; // adjust if your table name differs

    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'symbol',
        'rate',
        'language_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'symbol' => 'string',
        'rate' => 'decimal:6',
        'language_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
