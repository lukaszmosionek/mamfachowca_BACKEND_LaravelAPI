<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Photo;
use App\Models\ServiceTranslation;
use App\Notifications\NewMessageNotification;
use App\Services\MessageService;
use Exception;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    protected $providers;
    protected $clients;

    public function run(): void
    {

        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            // ServiceSeeder::class,
        ]);

        $this->createUsers();
        $this->sendMessages();
        $this->providerSeeder();
        $this->clientSeeder();
        // Usługi dla każdego provider'a
    }

    private function createUsers(){
        $this->providers = collect([
            User::factory()->create([
                'email' => 'provider@onet.pl',
                'password' => 'password',
                'role' => 'provider',
            ])
        ])->merge(
            User::factory()->count(3)->create(['role' => 'provider'])
        );

        $this->clients = collect([
            User::factory()->create([
                'email' => 'client@onet.pl',
                'password' => 'password',
                'role' => 'client',
            ])
        ])->merge(
            User::factory()->count(5)->create(['role' => 'client'])
        );
    }

    private function sendMessages(){
        for($i = 0; $i < 20; $i++) {
            (new MessageService())->sendMessage(
                $this->clients->random(),
                $this->providers->random(),
                'To jest treść testowej wiadomości ' . ($i + 1)
            );
        }
    }

    private function providerSeeder(){
        $this->providers->each(function ($provider) {
            $services = Service::factory()->count(30)->create([
                'provider_id' => $provider->id,
            ])->each(function ($service) {
                foreach (Language::all() as $language) {
                    ServiceTranslation::factory()->create([
                        'service_id' => $service->id,
                        'language_id' => $language->id,
                    ]);
                }
            });

            foreach($services as $service){
                $service->photos()->createMany( Photo::factory()->count( rand(2, 9) )->make()->toArray() );
            }

            foreach( config('constants.days') as $day ){
                if( rand(1,5) == 1 ) continue;
                Availability::factory()->for($provider, 'provider')->create( ['day_of_week' => $day] );
            }
        });
    }

    private function clientSeeder(){
        $this->clients->each(function ($client) {
            Appointment::factory()->count(20)->make()->each(function ($appointment) use ($client) {
                $service = Service::inRandomOrder()->first();

                DB::table('favorites')->insert([
                    'user_id' => $client->id,
                    'service_id' => $service->id,
                ]);

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
