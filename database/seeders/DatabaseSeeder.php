<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Appointment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'email' => 'provider@onet.pl',
            'password' => 'password',
            'role' => 'provider',
        ]);

        User::factory()->create([
            'email' => 'client@onet.pl',
            'password' => 'password',
            'role' => 'client',
        ]);

        // Providerzy
        $providers = User::factory()->count(3)->create([
            'role' => 'provider',
        ]);

        // Klienci
        $clients = User::factory()->count(5)->create([
            'role' => 'client',
        ]);

        // Usługi dla każdego provider'a
        $providers->each(function ($provider) {
            Service::factory()->count(3)->create([
                'user_id' => $provider->id,
            ]);
        });

        $this->call([
            ServiceSeeder::class,
        ]);

        $clients->each(function ($client) {
            Appointment::factory()->count(2)->make()->each(function ($appointment) use ($client) {
                $service = Service::inRandomOrder()->first();

                // dd($service);

                Appointment::create([
                    'client_id' => $client->id,
                    'service_id' => $service->id,
                    'provider_id' => $service->user_id,
                    'date' => $appointment->date,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                    'status' => $appointment->status,
                ]);
            });
        });
    }
}
