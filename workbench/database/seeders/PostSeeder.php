<?php

namespace Workbench\Database\Seeders;

use Workbench\App\Models\Post;
use Workbench\App\Models\Category;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::all()->each(function($category) {
            Post::factory()->count(12)->create([
                'category_id' => $category->id,
            ]);
        });

        // Ensure testing elements are correct

        $tech = Category::where('slug','tech')->first();

        Post::find(16)->update([
            'category_id' => $tech->id,
            'title' => 'Whitecube\'s open source packages',
            'slug' => str('Whitecube\'s open source packages')->slug(),
            'published_at' => now()->subDays(14)
        ]);
        
        Post::find(24)->update([
            'category_id' => $tech->id,
            'title' => 'Whitecube\'s laravel-link package review',
            'slug' => str('Whitecube\'s laravel-link package review')->slug(),
            'content' => 'Check out our other [package reviews](#link[posts.item@16]) and tell us what you think.',
            'published_at' => now()->subDays(10)
        ]);
    }
}
