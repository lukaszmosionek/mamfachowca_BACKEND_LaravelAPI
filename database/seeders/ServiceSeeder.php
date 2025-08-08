<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        for ( $i = 0; $i < 5; $i++) {
            $serviceModel = Service::factory()->create();
            $serviceModel->photos()->create( Photo::factory()->count( rand(2, 9) )->make()->toArray() );

        }
    }
}
