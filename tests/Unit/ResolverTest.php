<?php

use Whitecube\Links\Manager;
use Whitecube\Links\Resolvers\Route;
use Whitecube\Links\Resolvers\Archive;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Whitecube\Links\Tests\Fixtures\FakeModel;

expect()->extend('toBeWorkingArchiveResolver', function () {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\Archive::class);
    expect($this->value->key)->toBe('posts');

    $option = $this->value->toOption();
    expect($option)->toBeInstanceOf(\Whitecube\Links\Option::class);
    expect($option->getResolverKey())->toBe('posts');
    expect($option->getVariantKey())->toBeNull();
    expect($option->hasChoices())->toBeTrue();
});

expect()->extend('toBeWorkingArchiveIndexResolver', function () {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\ArchiveIndexRoute::class);
    expect($this->value->key)->toBe('posts.index');

    $option = $this->value->toOption();
    expect($option)->toBeInstanceOf(\Whitecube\Links\Option::class);
    expect($option->getResolverKey())->toBe('posts.index');
    expect($option->getVariantKey())->toBeNull();
    expect($option->hasChoices())->toBeFalse();

    expect($this->value->getRouteName())->toBe('posts');
    expect($this->value->getRouteParameters())->toBeEmpty();
});

expect()->extend('toBeWorkingArchiveItemsResolver', function (Collection $items) {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\ArchiveItemsRoute::class);
    expect($this->value->key)->toBe('posts.item');

    $options = $this->value->toOption();
    expect($options)->toBeInstanceOf(\Whitecube\Links\OptionsCollection::class);
    expect($options->total())->toBe($items->count());

    $keys = $items->pluck('id')->all();
    $titles = $items->pluck('title')->all();

    foreach($options->all() as $index => $option) {
        expect($option)->toBeInstanceOf(\Whitecube\Links\Option::class);
        expect($option->getResolverKey())->toBe('posts.item');
        expect($option->getVariantKey())->toBe($keys[$index]);
        expect($option->getTitle())->toBe($titles[$index]);
        expect($option->hasChoices())->toBeFalse();
    }

    expect($this->value->getRouteName())->toBe('post');

    foreach($this->value->getAllVariants() as $index => $variant) {
        expect($this->value->getRouteParameter('slug', $variant))->toBe($variant->slug);
    }
});

it('can register named route', function () {
    $service = new Manager();

    $resolver = $service->route('foo');

    expect($resolver)->toBeInstanceOf(Route::class);
    expect($resolver->getRouteName())->toBe('foo');
    expect($resolver->getRouteParameters())->toBeArray();
    expect($resolver->getRouteParameters())->toHaveCount(0);
    expect($resolver->getTitle())->toBeEmpty();

    expect($service->for('foo'))->toBe($resolver);
});

it('can register named route with route parameters and specific title', function () {
    $service = new Manager();

    $resolver = $service->route('foo', ['bar' => 'test'])->title('Foo Page');

    expect($resolver)->toBeInstanceOf(Route::class);
    expect($resolver->getRouteName())->toBe('foo');
    expect($resolver->getRouteParameters())->toBeArray();
    expect($resolver->getRouteParameters())->toHaveCount(1);
    expect($resolver->getRouteParameter('bar'))->toBe('test');
    expect($resolver->parameter('bar', 'foo'))->toBe($resolver);
    expect($resolver->getRouteParameter('bar'))->toBe('foo');
    expect($resolver->getTitle())->toBe('Foo Page');

    expect($service->for('foo'))->toBe($resolver);
});

it('can register empty resource archive that doesn\'t show up as a link option', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->title('Latest news');

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBe($resolver);
    expect($resolver->toOption())->toBeNull();
});

it('can register resource archive with index page only', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')
        ->title('Latest news')
        ->index(fn ($entry) => $entry->route('posts')->title('All latest news'));

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBe($resolver);
    expect($service->for('posts.index'))->toBeWorkingArchiveIndexResolver();
    expect($service->tryFor('posts.item'))->toBeNull();
    expect($resolver->toOption()?->getKey())->toBe($service->for('posts.index')?->toOption()?->getKey());
});

it('can register resource archive with index page and resource entries from array', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')
        ->title('Latest news')
        ->index(fn ($entry) => $entry->route('posts')->title('All latest news'))
        ->items(fn ($entry) => $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->collect(FakeModel::$items)
            ->keyBy('id')
        );

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->for('posts.index'))->toBeWorkingArchiveIndexResolver();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from collection', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->collect(collect(FakeModel::$items)->map(fn($item) => (object) $item))
            ->keyBy('id');
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from arrayable object', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->collect(new class() implements Arrayable {
                public function toArray() {
                    return collect(FakeModel::$items)->map(fn($item) => (object) $item)->all();
                }
            })
            ->keyBy('id');
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from closure', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->collect(fn() => collect(FakeModel::$items)->map(fn($item) => (object) $item)->all())
            ->keyBy('id');
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from query', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->query(fn () => FakeModel::query())
            ->keyBy('id');
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from model', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->model(FakeModel::class);
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items));
});

it('can register resource archive with resource entries from model query', function () {
    setupAppBindings();

    $service = new Manager();

    $resolver = $service->archive('posts')->items(function ($entry) {
        $entry->route('post')
            ->parameter('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->model(FakeModel::class, fn($query) => $query->limit(2));
    });

    expect($resolver)->toBeInstanceOf(Archive::class);
    expect($service->for('posts'))->toBeWorkingArchiveResolver();
    expect($service->tryFor('posts.index'))->toBeNull();
    expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(collect(FakeModel::$items)->take(2));
});
