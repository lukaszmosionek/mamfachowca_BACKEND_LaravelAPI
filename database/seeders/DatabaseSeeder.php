<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Availability;
use App\Notifications\NewMessageNotification;
use App\Services\MessageService;
use Exception;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clients = collect();
        $providers = collect();

        $provider = User::factory()->create([
            'email' => 'provider@onet.pl',
            'password' => 'password',
            'role' => 'provider',
        ]);

        $client = User::factory()->create([
            'email' => 'client@onet.pl',
            'password' => 'password',
            'role' => 'client',
        ]);

        $providers->push($provider);
        $clients->push($client);

        // Providerzy
        $providers = $providers->merge(User::factory()->count(3)->create([
            'role' => 'provider',
        ]));

        // Klienci
        $clients = $clients->merge(User::factory()->count(5)->create([
            'role' => 'client',
        ]));

        for($i = 0; $i < 20; $i++) {
            (new MessageService())->sendMessage(
                $clients->random(),
                $providers->random(),
                'To jest treść testowej wiadomości ' . ($i + 1)
            );
        }

        $this->call([
            ServiceSeeder::class,
        ]);

        // Usługi dla każdego provider'a
        $providers->each(function ($provider) {
            Service::factory()->count(3)->create([
                'provider_id' => $provider->id,
            ]);
            try{
                Availability::factory()->count(rand(1,5))->for($provider, 'provider')->create();
            }catch(Exception $e){
                // echo $e->getMessage();
            }
        });


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
