<?php

use Workbench\App\Models\Post;
use Whitecube\Links\Manager;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Link;
use Whitecube\Links\Facades\Links;

it('can resolve updated URL from shortkey after model changes', function() {
    $link = Link::fromKey('posts.item@16');

    expect($link->url)->toBe('http://localhost/blog/tech/whitecubes-open-source-packages');

    Post::find(16)->update(['slug' => 'whitecubes-open-source-packages-in-review']);

    $link = Link::fromKey('posts.item@16');

    expect($link->url)->toBe('http://localhost/blog/tech/whitecubes-open-source-packages-in-review');
});

it('can cast textual content with resolved URLs', function() {
    $post = Post::find(24);

    expect($post->content)->not()->toContain('posts.item@16');
    expect($post->content)->toContain('http://localhost/blog/tech/whitecubes-open-source-packages');
});
