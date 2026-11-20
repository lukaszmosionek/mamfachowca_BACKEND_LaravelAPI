<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceTranslation;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

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

        foreach ($services as $service) {
            $photos = array_values(array_filter(
                $service['photos'] ?? [],
                static fn ($photo) => is_string($photo)
                    && strtolower(pathinfo($photo, PATHINFO_EXTENSION)) === 'svg'
            ));
            if (!$photos) {
                continue;
            }

            $serviceModel = Service::factory()->create([
                'provider_id' => $randomProvider->id,
            ]);

            foreach (Language::all() as $language) {
                ServiceTranslation::factory()->create([
                    'service_id' => $serviceModel->id,
                    'language_id' => $language->id,
                    'name' => $service['name_'.$language->code],
                    'description' => $service['desc_'.$language->code] ?? '',
                ]);
            }

            $paths = [];
            foreach ($photos as $index => $photo) {
                $sourcePath = public_path($photo);
                if (!File::exists($sourcePath)) {
                    throw new RuntimeException("Service SVG does not exist: {$photo}");
                }

                $paths[$index] = (new ImageService())->storeSvgContents(File::get($sourcePath), basename($sourcePath));
                $paths[$index]['is_main'] = $index === 0;
            }

            $serviceModel->photos()->createMany($paths);
            // die();
            // if($index === 5) die();
        }


    }
}
