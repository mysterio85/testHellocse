<?php

namespace App\Domain\Comment\Factories;

use App\Domain\Administrator\Models\Administrator;
use App\Domain\Comment\Models\Comment;
use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content'          => fake()->sentence(10),
            'administrator_id' => Administrator::factory(),
            'profile_id'       => Profile::factory(),
        ];
    }
}
