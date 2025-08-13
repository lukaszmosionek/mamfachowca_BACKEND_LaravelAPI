<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use  HasFactory;

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
    // end of static methods
}
