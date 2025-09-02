<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Language extends Model
{
    use  HasFactory;

    protected static ?int $cachedId = null;

    //relationships
    public function currency()
    {
        return $this->hasOne(Currency::class);
    }
    // end of relationships

    // static methods
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
    // end of static methods
}
