<?php

use Whitecube\Links\Manager;
use Whitecube\Links\RouteResolver;
use Illuminate\Support\Facades\URL;

it('can register named route', function () {
   $service = new Manager();

   $resolver = $service->route('foo');

   expect($resolver)->toBeInstanceOf(RouteResolver::class);
   expect($resolver->getRouteName())->toBe('foo');
   expect($resolver->getRouteArguments())->toBeArray();
   expect($resolver->getRouteArguments())->toHaveCount(0);

   expect($service->for('foo'))->toBe($resolver);
});

it('can register named route with arguments', function () {
   $service = new Manager();

   $resolver = $service->route('foo', [
      'bar' => 'test',
   ]);

   expect($resolver)->toBeInstanceOf(RouteResolver::class);
   expect($resolver->getRouteName())->toBe('foo');
   expect($resolver->getRouteArguments())->toBeArray();
   expect($resolver->getRouteArguments())->toHaveCount(1);
   expect($resolver->getRouteArgument('bar'))->toBe('test');

   expect($service->for('foo'))->toBe($resolver);
});

it('can resolve simple defined route', function () {
   URL::shouldReceive('route')
      ->once()
      ->with('foo', [])
      ->andReturn('https://foo.bar/testing-route');

   $resolver = (new RouteResolver('some-key'))->route('foo');

   expect($resolver->resolve())->toBe('https://foo.bar/testing-route');
});

it('can resolve defined route with arguments', function () {
   URL::shouldReceive('route')
      ->once()
      ->with('foo', ['bar' => 'test', 'foo' => 'bar'])
      ->andReturn('https://foo.bar/testing-route');

   $resolver = (new RouteResolver('some-key'))->route('foo', ['bar' => 'overwritten', 'foo' => 'bar']);

   expect($resolver->resolve(['bar' => 'test']))->toBe('https://foo.bar/testing-route');
});
