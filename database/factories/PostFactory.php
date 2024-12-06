<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph(5),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'is_private' => $this->faker->randomElement([0,1]),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
