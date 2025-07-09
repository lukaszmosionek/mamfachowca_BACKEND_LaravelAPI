<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Pobierz tylko użytkowników z rolą "provider"
        $providers = User::where('role', 'provider')->get();

        foreach ($providers as $provider) {
            // Każdy provider dostaje 3–5 usług
            Service::factory()
                ->count(rand(3, 5))
                ->create(['provider_id' => $provider->id]);
        }
    }
}
