<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {

    // $randomProvider = User::where('role', 'provider')->inRandomOrder()->first();

        for ( $i = 0; $i < 5; $i++) {
            // Każdy provider dostaje 3–5 usług
            $serviceModel = Service::factory()->create();
            // dd($serviceModel->name);

            for ($j = 0; $j < rand(2, 9); $j++) {
                $serviceModel->photos()->create([
                    'thumbnail' => generatePlaceholder(300, 300),
                    'medium' => generatePlaceholder(300, 300),
                    'large' => generatePlaceholder(300, 300),
                ]);
            }


        }
    }
}
