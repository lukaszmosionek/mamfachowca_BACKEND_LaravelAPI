<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {

        $services = [
            'en' => [
                'Fence Painting',
                'Washing Machine Repair',
                'House Cleaning',
                'Window Installation',
                'Roof Inspection',
                'Carpet Cleaning',
                'Lawn Mowing',
                'Gutter Cleaning',
                'Plumbing Repair',
                'Electrical Wiring',
                'Air Conditioner Servicing',
                'Refrigerator Repair',
                'Drywall Installation',
                'Deck Staining',
                'Garage Door Repair',
                'Tile and Grout Cleaning',
                'Pest Control',
                'Interior Painting',
                'Exterior Painting',
                'Water Heater Repair',
                'Chimney Sweeping',
                'Tree Trimming',
                'Security Camera Installation',
                'Smart Thermostat Setup',
                'Furniture Assembly',
                'Ceiling Fan Installation',
                'Pressure Washing',
                'Siding Repair',
                'Basement Waterproofing',
                'Driveway Sealing',
            ],
            'pl' => [
                'Malowanie Płotu',
                'Naprawa Pralki',
                'Sprzątanie Domu',
                'Montaż Okien',
                'Inspekcja Dachu',
                'Czyszczenie Dywanów',
                'Koszenie Trawnika',
                'Czyszczenie Rynien',
                'Naprawa Instalacji Wodnej',
                'Instalacja Elektryczna',
                'Serwis Klimatyzacji',
                'Naprawa Lodówki',
                'Montaż Płyt Gipsowych',
                'Bejcowanie Tarasu',
                'Naprawa Drzwi Garażowych',
                'Czyszczenie Płytek i Fugi',
                'Zwalczanie Szkodników',
                'Malowanie Wnętrz',
                'Malowanie Elewacji',
                'Naprawa Podgrzewacza Wody',
                'Czyszczenie Komina',
                'Przycinanie Drzew',
                'Montaż Kamer Monitoringu',
                'Instalacja Inteligentnego Termostatu',
                'Montaż Mebli',
                'Montaż Wentylatora Sufitowego',
                'Mycie Ciśnieniowe',
                'Naprawa Elewacji',
                'Hydroizolacja Piwnicy',
                'Uszczelnianie Podjazdu',
            ],
        ];

        $lang = $this->faker->randomElement(['pl', 'en']);

        return [
            'provider_id' => User::factory(),
            'name' => $this->faker->randomElement($services[$lang]),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(0, 50, 300),
            'duration_minutes' => $this->faker->numberBetween(15, 90),
            'lang' => $lang,
        ];

    }
}
