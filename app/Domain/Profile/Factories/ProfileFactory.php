<?php

namespace App\Domain\Profile\Factories;

use App\Domain\Administrator\Models\Administrator;
use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'administrator_id' => Administrator::factory(),
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'image_path' => 'profiles/default.png',
            'status' => fake()->randomElement(['inactive', 'pending', 'active']),
        ];
    }
}
