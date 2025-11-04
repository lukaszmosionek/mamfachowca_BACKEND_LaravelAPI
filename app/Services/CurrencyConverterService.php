<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyConverterService
{
    protected $cacheKey = 'currency_rates';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get all currency rates from cache or database
     */
    protected function getRates(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return Currency::pluck('rate', 'code')->toArray();
        });
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $from, string $to): float
    {
        $rates = $this->getRates();

        $from = strtoupper($from);
        $to = strtoupper($to);

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            Log::warning("CurrencyConverter: Unknown currency: {$from} or {$to}");
            return $amount; // fallback, no conversion
        }

        // Convert via base currency (USD)
        $amountInBase = $amount * $rates[$from];
        $converted = $amountInBase / $rates[$to];

        return round($converted, 2);
    }

    /**
     * Clear cache manually (e.g. when rates update)
     */
    public function refreshCache(): void
    {
        Cache::forget($this->cacheKey);
        $this->getRates(); // repopulate cache
    }
}
