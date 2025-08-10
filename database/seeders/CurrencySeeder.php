<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        Currency::insert([
            [
                'code' => 'USD',
                'symbol' => '$',
                'rate' => 1,
                'language_id' => $languages->where('code', 'en')->first()->id
             ],
            [
                'code' => 'PLN',
                'symbol' => 'zł',
                'rate' => 4.5,
                'language_id' => $languages->where('code', 'pl')->first()->id
                ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'rate' => 0.9,
                'language_id' => $languages->where('code', 'en')->first()->id
            ],
        ]);
    }
}
