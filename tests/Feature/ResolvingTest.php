<?php

use Workbench\App\Models\Post;
use Whitecube\Links\Manager;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Link;
use Whitecube\Links\Facades\Links;

it('can resolve URL from shortkey after model changes', function() {
    expect(Link::fromKey('posts.item@16')->url)->toBe('http://localhost/blog/tech/whitecubes-open-source-packages');

    Post::find(16)->update(['slug' => 'whitecubes-open-source-packages-in-review']);

    expect(Link::fromKey('posts.item@16')->url)->toBe('http://localhost/blog/tech/whitecubes-open-source-packages-in-review');
});

it('can resolve multiple Query Variants during the same request lifecycle', function() {
    expect(Link::fromKey('posts.item@16')->url)->toBe('http://localhost/blog/tech/whitecubes-open-source-packages');
    expect(Link::fromKey('posts.item@24')->url)->toBe('http://localhost/blog/tech/whitecubes-laravel-link-package-review');
});

it('can parse textual content with resolved URLs using model attribute casting', function() {
    $post = Post::find(24);

    expect($post->content)->not()->toContain('posts.item@16');
    expect($post->content)->toContain('http://localhost/blog/tech/whitecubes-open-source-packages');
});

it('can parse textual content with resolved URLs using Links facade', function() {
    expect(false)->toBeTrue();
});

it('can parse textual content with resolved URLs using Laravel\'s string helpers', function() {
    expect(false)->toBeTrue();
});

it('can parse textual content with resolved URLs using blade directive', function() {
    expect(false)->toBeTrue();
});

it('can report resolver issues when parsing textual content using model attribute casting', function() {
    expect(false)->toBeTrue();
});

it('can report resolver issues when parsing textual content using Links facade', function() {
    expect(false)->toBeTrue();
});

it('can report resolver issues when parsing textual content using Laravel\'s string helpers', function() {
    expect(false)->toBeTrue();
});

it('can report resolver issues when parsing textual content using blade directive', function() {
    expect(false)->toBeTrue();
});
