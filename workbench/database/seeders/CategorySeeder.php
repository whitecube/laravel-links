<?php

namespace Workbench\Database\Seeders;

use Workbench\App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'slug' => 'travel',
            'title' => 'Travel stories',
        ]);

        Category::create([
            'slug' => 'cooking',
            'title' => 'In the kitchen',
        ]);

        Category::create([
            'slug' => 'tech',
            'title' => 'Web development',
        ]);

        Category::create([
            'slug' => 'reading',
            'title' => 'My library',
        ]);
    }
}
