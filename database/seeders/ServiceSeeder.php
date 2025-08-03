<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {

    $randomProvider = User::where('role', 'provider')->inRandomOrder()->first();

        for ($i=1; $i<30; $i++) {
            // Każdy provider dostaje 3–5 usług
            $serviceModel = Service::factory()->create();
            $serviceModel->photos()->create();
        }
    }
}
