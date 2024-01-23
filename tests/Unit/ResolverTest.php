<?php

use Whitecube\Links\Manager;
use Whitecube\Links\Resolvers\Route;
use Illuminate\Support\Facades\URL;

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
   $service = new Manager();

   $resolver = $service->archive('posts')
      ->title('Latest news')
      ->index(fn ($entry) => $entry->route('posts')->title('All latest news'))
      ->items(fn ($entry) => $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect([
            ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ])
      );
});

it('can register resource archive with resource entries from collection', function () {
   $service = new Manager();

   $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect(collect([
            (object) ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            (object) ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            (object) ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ]));
      });
});

it('can register resource archive with resource entries from closure', function () {
   $service = new Manager();

   $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->collect(fn() => [
            (object) ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
            (object) ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
            (object) ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
         ]);
   });
});

it('can register resource archive with resource entries from model', function () {
   $service = new Manager();

   $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->model(FakeModel::class);
   });
});

it('can register resource archive with resource entries from model query', function () {
   $service = new Manager();

   $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->model(FakeModel::class, fn($query) => $query->testScope());
   });
});

it('can register resource archive with resource entries from query', function () {
   $service = new Manager();

   $service->archive('posts')->items(function ($entry) {
      $entry->route('post')
         ->argument('slug', fn ($item) => $item->slug)
         ->title(fn ($item) => $item->title)
         ->query(fn () => FakeModel::query());
   });
});

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
