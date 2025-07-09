<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Availability;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clients = collect();
        $providers = collect();

         $providers->push(User::factory()->create([
            'email' => 'provider@onet.pl',
            'password' => 'password',
            'role' => 'provider',
        ]));

        $clients->push(User::factory()->create([
            'email' => 'client@onet.pl',
            'password' => 'password',
            'role' => 'client',
        ]));

        // Providerzy
        $providers = $providers->merge(User::factory()->count(3)->create([
            'role' => 'provider',
        ]));

        // Klienci
        $clients = $clients->merge(User::factory()->count(5)->create([
            'role' => 'client',
        ]));

        // Usługi dla każdego provider'a
        $providers->each(function ($provider) {
            Service::factory()->count(3)->create([
                'provider_id' => $provider->id,
            ]);
            Availability::factory()->for($provider, 'provider')->create();
        });

        $this->call([
            ServiceSeeder::class,
        ]);

        $clients->each(function ($client) {
            Appointment::factory()->count(20)->make()->each(function ($appointment) use ($client) {
                $service = Service::inRandomOrder()->first();

                // dd($service);

                Appointment::create([
                    'client_id' => $client->id,
                    'service_id' => $service->id,
                    'provider_id' => $service->provider_id,
                    'date' => $appointment->date,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                    'status' => $appointment->status,
                ]);
            });
        });
    }
}
