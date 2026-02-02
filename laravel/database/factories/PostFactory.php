<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'status' => 'published',
            'content' => $this->faker->paragraph,
        ];
    }

    // Optionnel : méthode pour créer un post brouillon
    public function draft()
    {
        return $this->state(fn () => [
            'status' => 'draft',
        ]);
    }
}