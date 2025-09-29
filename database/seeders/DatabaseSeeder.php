<?php

namespace Database\Seeders;

use App\Enum\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Language;
use App\Models\Photo;
use App\Models\ServiceTranslation;
use App\Services\MessageService;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    protected $providers;
    protected $clients;

    protected $count;

    public function  __construct(){
        $this->count = [
            'services' => 5,// for each provider
            'clients' => 30,
            'providers' => 20,
            'appointments' => 3,// for each client
        ];
    }

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

        User::factory()->create([
            'name' => 'Admin Due',
            'email' => 'admin@onet.pl',
            'password' => 'password',
            'role' => Role::ADMIN,
        ]);

        $this->providers = collect([
            User::factory()->create([
                'name' => 'Provider Due',
                'email' => 'provider@onet.pl',
                'password' => 'password',
                'role' => Role::PROVIDER,
            ])
        ])->merge(
            User::factory()->count( $this->count['providers'] )->create(['role' => Role::PROVIDER])
        );

        $this->clients = collect([
            User::factory()->create([
                'name' => 'Client Due',
                'email' => 'client@onet.pl',
                'password' => 'password',
                'role' => Role::CLIENT,
            ])
        ])->merge(
            User::factory()->count( $this->count['clients'] )->create(['role' => Role::CLIENT])
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

        $languages = Language::all();

        $this->providers->each(function ($provider) use ($languages) {
            $services = Service::factory()->withTranslations()->count( $this->count['services'] )->create([
                'provider_id' => $provider->id,
            ]);

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
            Appointment::factory()->count( $this->count['appointments'] )->make()->each(function ($appointment) use ($client) {
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
