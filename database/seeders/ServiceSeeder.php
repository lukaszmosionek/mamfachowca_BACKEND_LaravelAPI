<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
        [
            'name_en' => 'Fence Painting',
            'name_pl' => 'Malowanie płotu',
            'photo' => 'https://cdn.mos.cms.futurecdn.net/bXAmxe5X7aEEfuTvFZzwLQ.jpg',
        ],
        [
            'name_en' => 'Washing Machine Repair',
            'name_pl' => 'Naprawa pralki',
            'photo' => 'https://thebtc.co.uk/wp-content/uploads/elementor/thumbs/Washing-Machine-RS-1-qwrhic2pq5rx2le46aa1fsf50aahpwi7j8vcukhzt4.png',
        ],
        [
            'name_en' => 'House Cleaning',
            'name_pl' => 'Sprzątanie domu',
            'photo' => 'https://www.bondcleaninginsunshinecoast.com.au/wp-content/uploads/2022/04/sad-cleaning-women.jpg',
        ],
        [
            'name_en' => 'Window Installation',
            'name_pl' => 'Montaż okien',
            'photo' => 'https://images.contentstack.io/v3/assets/bltf589e66bcaecd79c/bltebcfc5a788e0fa43/67a26b0a9dbc8daa3eba4fe9/installing-replacement-window-pella-impervia-replacing-a-replacement-window.jpg?width=1152&height=766&format=jpg&quality=90',
        ],
        [
            'name_en' => 'Roof Inspection',
            'name_pl' => 'Inspekcja dachu',
            'photo' => 'https://lirp.cdn-website.com/3c617ef1/dms3rep/multi/opt/roof-inspection-640w.jpeg',
        ],
        [
            'name_en' => 'Carpet Cleaning',
            'name_pl' => 'Czyszczenie dywanów',
            'photo' => 'https://img.choice.com.au/-/media/74fe12e385064f56baa7164473f2ca20.ashx',
        ],
        [
            'name_en' => 'Lawn Mowing',
            'name_pl' => 'Koszenie trawnika',
            'photo' => 'https://images.immediate.co.uk/production/volatile/sites/10/2020/07/2048-1365-Einfell-001-d1c43fd.jpg?resize=1200%2C630',
        ],
        [
            'name_en' => 'Gutter Cleaning',
            'name_pl' => 'Czyszczenie rynien',
            'photo' => 'https://www.centralbayroofing.com/wp-content/uploads/2017/09/gutter-cleaning-5-diy-tips-to-get-it-done-in-no-time.jpg',
        ],
        [
            'name_en' => 'Plumbing Repair',
            'name_pl' => 'Naprawa hydrauliki',
            'photo' => 'https://sierraair.com/wp-content/uploads/2023/10/New-Header-Image-10_4_23-scaled.jpg',
        ],
        [
            'name_en' => 'Electrical Wiring',
            'name_pl' => 'Instalacja elektryczna',
            'photo' => 'https://cdn.prod.website-files.com/643dd13153ce80ea0a9ceae9/66bcfa65cd99cbcce70e919c_Untitled%20(98).jpg',
        ],
        [
            'name_en' => 'Air Conditioner Servicing',
            'name_pl' => 'Serwis klimatyzacji',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9veFoofEBF_gbVx9OG57lVFWzdQUM7zGRRQ&s',
        ],
        [
            'name_en' => 'Refrigerator Repair',
            'name_pl' => 'Naprawa lodówki',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRONPGtVoljTpH3m6JOcrYFUbhFE_Awb-ybfg&s',
        ],
        [
            'name_en' => 'Drywall Installation',
            'name_pl' => 'Montaż płyt gipsowych',
            'photo' => 'https://d3q01gc7kwv7n6.cloudfront.net/images/sites/newsroom/cards/Gypsum-Board_HSLite_1280x500.png',
        ],
        [
            'name_en' => 'Deck Staining',
            'name_pl' => 'Malowanie tarasu',
            'photo' => 'https://fixthisbuildthat.com/wp-content/uploads/2020/06/How-to-Stain-a-Deck-the-Easy-Way.jpg',
        ],
        [
            'name_en' => 'Garage Door Repair',
            'name_pl' => 'Naprawa drzwi garażowych',
            'photo' => 'https://americangaragedoor.net/wp-content/uploads/2025/01/Austin-Technician-Scott-Howard-scaled.jpg',
        ],
        [
            'name_en' => 'Tile and Grout Cleaning',
            'name_pl' => 'Czyszczenie płytek i fug',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQQEYccSToFEbj9bBNVI5SS0FtNr8z7p0ZBQ&s',
        ],
        [
            'name_en' => 'Pest Control',
            'name_pl' => 'Zwalczanie szkodników',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSmOJANmLmXNZTuIBHgIHzbHciUAZySvuTbUA&s',
        ],
        [
            'name_en' => 'Interior Painting',
            'name_pl' => 'Malowanie wnętrz',
            'photo' => 'https://lirp.cdn-website.com/828b2c8a/dms3rep/multi/opt/AdobeStock_216970582-960w.jpeg',
        ],
        [
            'name_en' => 'Exterior Painting',
            'name_pl' => 'Malowanie elewacji',
            'photo' => 'https://customcolonialpainting.com/wp-content/uploads/2022/05/Can-You-Paint-Your-Own-House-Exterior_-scaled.jpg',
        ],
        [
            'name_en' => 'Water Heater Repair',
            'name_pl' => 'Naprawa podgrzewacza wody',
            'photo' => 'https://metropha.com/wp-content/uploads/2019/05/5-Factors-That-Result-in-a-Leak-in-Water-Heaters-_-Water-Heater-Repair-in-Cleveland-TN.jpg',
        ],
        [
            'name_en' => 'Chimney Sweeping',
            'name_pl' => 'Czyszczenie komina',
            'photo' => 'https://mcdhomeandgarden.ie/wp-content/uploads/2021/11/Chimney-Cleaning.jpg',
        ],
        [
            'name_en' => 'Tree Trimming',
            'name_pl' => 'Przycinanie drzew',
            'photo' => 'https://treenewal.com/wp-content/uploads/2022/08/tree_trimming_and_tree_pruning.jpg',
        ],
        [
            'name_en' => 'Security Camera Installation',
            'name_pl' => 'Instalacja kamer bezpieczeństwa',
            'photo' => 'https://lh4.googleusercontent.com/proxy/Xh8Uq8pOQy1_abN8WH7SnwNyC4K2SAT_Jo-wQNPJtOUfOzkytRl4k7Xl0kbMMIY-F0dFJiUIUQ945UvrZoT8ZpBEhbsIigZbaATnepIbh1Wm8uIPFQCZ8Q2AXKvwdgksdx0-2-I9DYueqjsnrWb4vw',
        ],
        [
            'name_en' => 'Smart Thermostat Setup',
            'name_pl' => 'Montaż inteligentnego termostatu',
            'photo' => 'https://www.thespruce.com/thmb/BObpYCBqPddOYYXhVI6ceMeAQuE=/5378x3585/filters:no_upscale()/SPR-smart-thermostat-installation-7109495-step-hero-f2525e5acfdf476ea8a2bfde7efdb037.jpg',
        ],
        [
            'name_en' => 'Furniture Assembly',
            'name_pl' => 'Montaż mebli',
            'photo' => 'https://www.furniture123.co.uk/Files/images/f123/F123_Assembly_Hero.jpg',
        ],
        [
            'name_en' => 'Ceiling Fan Installation',
            'name_pl' => 'Montaż wentylatora sufitowego',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTtqg3zfdfiA1K-utqhe0C1aftXRhWok_vOBw&s',
        ],
        [
            'name_en' => 'Pressure Washing',
            'name_pl' => 'Mycie ciśnieniowe',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHByTMWbJj4vP_a4If-5BWbfvDlB_vbg5QqA&s',
        ],
        [
            'name_en' => 'Siding Repair',
            'name_pl' => 'Naprawa elewacji',
            'photo' => 'https://www.ebyexteriors.com/wp-content/uploads/2017/10/siding-repair-replacement-1024x682.jpg',
        ],
        [
            'name_en' => 'Basement Waterproofing',
            'name_pl' => 'Izolacja przeciwwilgociowa piwnicy',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRgxnaxlKlSLalznS68ucSqNLRt1xrJmf8IA&s',
        ],
        [
            'name_en' => 'Driveway Sealing',
            'name_pl' => 'Uszczelnianie podjazdu',
            'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtPMLWdt9b0oJ6ZzpQ8E2I5cXOxmx3g_Vkbg&s',
        ]
    ];

    $randomProvider = User::where('role', 'provider')->inRandomOrder()->first();

        foreach ($services as $service) {
            // Każdy provider dostaje 3–5 usług

            $serviceModel = Service::factory()->create([
                    'provider_id' => $randomProvider->id,
                    'name' => $service['name_en']
            ]);

            $serviceModel->photos()->create([
                'photo_path' => $service['photo'],
                'is_main' => true
            ]);
        }
    }
}
