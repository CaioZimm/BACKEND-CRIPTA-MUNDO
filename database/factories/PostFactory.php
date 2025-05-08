<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image = fake()->imageUrl(640, 480);
        
        return [
            'user_id' => User::where('usertype', 'admin')->pluck('id')->random(),
            'title' => fake()->sentence(3),
            'caption' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => $image,
        ];
    }
}
