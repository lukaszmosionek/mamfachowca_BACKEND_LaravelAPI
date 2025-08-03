<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Services\GoogleImageSearchService;
use App\Services\ImageService;
use Exception;
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

        dump("Json File Error: ".json_last_error_msg());

        $randomProvider = User::where('role', 'provider')->inRandomOrder()->first();

        foreach ($services as $index => $service) {

            $serviceModel = Service::factory()->create([
                'provider_id' => $randomProvider->id,
                'name' => $service['name_en'],
                'description' => $service['desc_en'] ?? '',
            ]);

            $photos = (new GoogleImageSearchService)->searchImages( $service['name_en'], rand(2,5) );

            if( !$photos ) continue;

            foreach($photos as $index => $photo){
                try{
                    $paths[$index] = ( new ImageService() )->storeImageFromUrl($photo);
                    $paths[$index]['is_main'] =  $index === 0 ? true : false;
                }catch(Exception $e){
                    dump($e->getMessage());
                }
            }

            $serviceModel->photos()->createMany($paths);
        }

        die();
        if($index === 1) die();

    }
}
