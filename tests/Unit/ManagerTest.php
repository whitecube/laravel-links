<?php

use Whitecube\Links\Manager;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Exceptions\ResolverNotFound;
use Whitecube\Links\Tests\Fixtures\FakeResolver;

it('can define and find a resolver instance', function () {
   $service = new Manager();
   $resolver = new FakeResolver();

   $service->register('foo', $resolver);

   expect($service->for('foo'))->toBe($resolver);
});

it('cannot find an undefined resolver instance', function () {
   $service = new Manager();

   $service->for('foo');
})->throws(ResolverNotFound::class, 'Link resolver for key "foo" is undefined.');

it('cannot find an undefined resolver instance and return null', function () {
   $service = new Manager();

   expect($service->tryFor('foo'))->toBeNull();
});
