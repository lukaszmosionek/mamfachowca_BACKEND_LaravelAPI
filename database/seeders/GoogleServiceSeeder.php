<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Services\GoogleImageSearchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GoogleServiceSeeder extends Seeder
{
    public function run(): void
    {

        //chatgpt query
        // Create json file with services like(fence painting , Washing Machine Repair, House Cleaning) 20 records
        // [
        //     'name_en' => 'Driveway Sealing',
        //     'name_pl' => 'Uszczelnianie podjazdu',
        //      'desc_en' => 'en desc',
        //      'desc_pl' => 'pl opis'
        // ]

            $file = File::get(resource_path('js/services.json'));
            $services = json_decode($file, true);


            $randomProvider = User::where('role', 'provider')->inRandomOrder()->first();

                foreach ($services as $index => $service) {
                    // Każdy provider dostaje 3–5 usług

                    $serviceModel = Service::factory()->create([
                            'provider_id' => $randomProvider->id,
                            'name' => $service['name_en'],
                            'description' => $service['desc_en'],
                    ]);

                    if($index === 3) die();

                    // $photos = (new GoogleImageSearchService)->searchImages( $service['name_en'], rand(2,3) );

                    // foreach($photos as $index => $photo){
                    //     $serviceModel->photos()->create([
                    //         'photo_path' => $photo['link'],
                    //         'is_main' => $index === 0 ? true : false
                    //     ]);
                    // }
                }
            }
}
