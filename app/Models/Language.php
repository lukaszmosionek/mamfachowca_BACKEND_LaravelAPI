<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Language extends Model
{
    use  HasFactory;

    protected static ?int $cachedId = null;

    protected $table = 'languages'; // adjust if your table name differs

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Static methods
    |--------------------------------------------------------------------------
    */
    public static function codeIdMap()
    {
        return self::pluck('id', 'code');
    }

    public static function getCurrencyForCurrentLocale(bool $getCurrencyCode = true): ?string
    {
        $language = self::where('code', app()->getLocale())
            ->with('currency')
            ->first();

        return $getCurrencyCode ? $language?->currency?->code : $language;
    }

    public static function getCurrentLanguageId(): ?int
    {
        if (static::$cachedId === null) {
            static::$cachedId = static::query()
                ->where('code', app()->getLocale())
                ->value('id');
        }

        return static::$cachedId;
    }
}
