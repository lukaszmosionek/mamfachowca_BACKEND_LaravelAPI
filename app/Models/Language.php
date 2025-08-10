<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use  HasFactory;

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    public static function getCurrencyForCurrentLocale(bool $getCurrencyCode = true): ?string
    {
        $language = self::where('code', app()->getLocale())
            ->with('currency')
            ->first();

        return $getCurrencyCode ? $language?->currency?->code : $language;
    }
}
