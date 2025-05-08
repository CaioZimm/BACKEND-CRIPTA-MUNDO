<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $photo = url()->previous("storage/profile/profile.png");
        
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'usertype' => fake()->randomElement(['admin', 'user']),
            'photo' => $photo,
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
