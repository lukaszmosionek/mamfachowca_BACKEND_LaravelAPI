<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyConversionService
{
    /**
     * Convert amount from one currency to another
     *
     * @param float $amount
     * @param string $fromCurrencyCode e.g. 'USD'
     * @param string $toCurrencyCode e.g. 'EUR'
     * @return float|null
     */
    public function convert(float $amount, string $fromCurrencyCode, string $toCurrencyCode): ?float
    {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return $amount;
        }

        $fromCurrency = Currency::where('code', $fromCurrencyCode)->first();
        $toCurrency = Currency::where('code', $toCurrencyCode)->first();

        if (!$fromCurrency || !$toCurrency) {
            throw new \Exception('fromCurrency or toCurrency not found in CurrencyConversionService convert()');
        }

        // Assume rate is relative to a base currency (e.g., USD)
        // Conversion formula: amount * (toRate / fromRate)
        $convertedAmount = $amount * ($toCurrency->rate / $fromCurrency->rate);

        return round($convertedAmount, 6);
    }
}
