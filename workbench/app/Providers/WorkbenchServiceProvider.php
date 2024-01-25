<?php

namespace Workbench\App\Providers;

use Workbench\App\Models\Post;
use Whitecube\Links\Facades\Links;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Links::route('home')->title('Homepage');

        Links::archive('posts')
            ->index(fn($entry) => $entry->route('posts.index')->title('Blog'))
            ->items(fn($entry) => $entry->route('posts.item')
                ->model(Post::class, fn($query) => $query->with('category')->published())
                ->title(fn($post) => $post->title)
                ->parameter('category', fn($post) => $post->category->slug)
                ->parameter('post', fn($post) => $post->slug)
            );
    }
}
