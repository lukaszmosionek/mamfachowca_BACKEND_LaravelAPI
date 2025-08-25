<?php

namespace Database\Factories;

use App\Enum\Role;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $files = Storage::disk('public')->allFiles('example_avatars');
        $randomFile = $files[array_rand($files)];


        $firstname = fake()->firstName();
        $lastname = fake()->lastName();
        return [
            'name' => $firstname . ' ' . $lastname,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar' => (new ImageService)->resizeImage($randomFile, 300, 300),
            // 'avatar' => generatePlaceholder(300, 300 , ''.mb_substr($firstname, 0, 1).mb_substr($lastname, 0, 1)),
            'role' => Role::CLIENT,
            'lang' => fake()->randomElement( config('app.languages') ),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
