<?php

use Whitecube\Links\Manager;
use Whitecube\Links\Resolvers\Route;
use Whitecube\Links\Resolvers\Archive;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Support\Arrayable;
use Whitecube\Links\Tests\Fixtures\FakeModel;

expect()->extend('toBeWorkingArchiveResolver', function () {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\Archive::class);
    expect($this->value->key)->toBe('posts');
});

expect()->extend('toBeWorkingArchiveIndexResolver', function () {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\ArchiveIndexRoute::class);
    expect($this->value->key)->toBe('posts.index');

    $option = $this->value->toOption();
    expect($option)->toBeInstanceOf(\Whitecube\Links\Option::class);
    expect($option->getResolverKey())->toBe('posts.index');
    expect($option->getVariantKey())->toBeNull();
});

expect()->extend('toBeWorkingArchiveItemsResolver', function (int $count = 3) {
    $this->toBeInstanceOf(\Whitecube\Links\Resolvers\ArchiveItemsRoute::class);
    expect($this->value->key)->toBe('posts.item');

    $options = $this->value->toOption();
    expect($options)->toBeInstanceOf(\Whitecube\Links\OptionsCollection::class);
    expect($options->total())->toBe($count);

    $option = $options->first();
    expect($option)->toBeInstanceOf(\Whitecube\Links\Option::class);
    expect($option->getResolverKey())->toBe('posts.item');
    dd($option);
});

it('can register simple named route', function () {
   $service = new Manager();

   $resolver = $service->route('foo');

   expect($resolver)->toBeInstanceOf(Route::class);
   expect($resolver->getRouteName())->toBe('foo');
   expect($resolver->getRouteArguments())->toBeArray();
   expect($resolver->getRouteArguments())->toHaveCount(0);
   expect($resolver->getTitle())->toBe('foo');

   expect($service->for('foo'))->toBe($resolver);
});

it('can register named route with default route parameters and specific title', function () {
   $service = new Manager();

   $resolver = $service->route('foo', ['bar' => 'test'])
      ->title('Foo Page');

   expect($resolver)->toBeInstanceOf(Route::class);
   expect($resolver->getRouteName())->toBe('foo');
   expect($resolver->getRouteArguments())->toBeArray();
   expect($resolver->getRouteArguments())->toHaveCount(1);
   expect($resolver->getRouteArgument('bar'))->toBe('test');
   expect($resolver->getTitle())->toBe('Foo Page');

   expect($service->for('foo'))->toBe($resolver);
});

it('can register resource archive with index page and resource entries from array', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')
      ->title('Latest news')
      ->index(fn ($entry) => $entry->route('posts')->title('All latest news'))
      ->items(fn ($entry) => $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect([
            ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ])
      );

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.index'))->toBeWorkingArchiveIndexResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from collection', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect(collect([
            (object) ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            (object) ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            (object) ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ]));
      });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from arrayable object', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect(new class() implements Arrayable {
            public function toArray() {
               return [
                  (object) ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
                  (object) ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
                  (object) ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
               ];
            }
         });
      });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from closure', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect(fn() => [
            (object) ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            (object) ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            (object) ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ]);
   });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from query', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->query(fn () => FakeModel::query());
   });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from model', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->model(FakeModel::class);
   });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver();
})->only();

it('can register resource archive with resource entries from model query', function () {
   setupAppBindings();

   $service = new Manager();

   $resolver = $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         // ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->model(FakeModel::class, fn($query) => $query->limit(2));
   });

   expect($resolver)->toBeInstanceOf(Archive::class);
   expect($service->for('posts'))->toBeWorkingArchiveResolver();
   expect($service->for('posts.item'))->toBeWorkingArchiveItemsResolver(count: 2);
})->only();

it('can resolve simple defined route', function () {
   URL::shouldReceive('route')
      ->once()
      ->with('foo', [])
      ->andReturn('https://foo.bar/testing-route');

   $resolver = (new Route('some-key'))->route('foo');

   expect($resolver->resolve())->toBe('https://foo.bar/testing-route');
});

it('can resolve defined route with arguments', function () {
   URL::shouldReceive('route')
      ->once()
      ->with('foo', ['bar' => 'test', 'foo' => 'bar'])
      ->andReturn('https://foo.bar/testing-route');

   $resolver = (new Route('some-key'))->route('foo', ['bar' => 'overwritten', 'foo' => 'bar']);

   expect($resolver->resolve(['bar' => 'test']))->toBe('https://foo.bar/testing-route');
});
