<?php

namespace Database\Seeders;

use App\Domain\Administrator\Models\Administrator;
use App\Domain\Comment\Models\Comment;
use App\Domain\Profile\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Administrator::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'test',
        ]);

        Administrator::factory(5)->create()->each(function ($administrator) {
            Profile::factory(3)->create(['administrator_id' => $administrator->id])
                ->each(function ($profile) use ($administrator) {
                    Comment::factory()->create([
                        'administrator_id' => $administrator->id,
                        'profile_id'       => $profile->id,
                    ]);
                });
        });
    }
}
