<?php

namespace Workbench\Database\Factories;

use Workbench\App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $title = trim(fake()->unique()->sentence(rand(2,16)), '.');

        return [
            'title' => $title,
            'slug' => str($title)->slug(),
            'content' => fake()->paragraph(),
            'published_at' => rand(0,1) ? null : now()->subSeconds(rand(60,365*24*60*60)),
        ];
    }
}