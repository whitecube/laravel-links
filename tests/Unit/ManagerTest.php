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

it('can register macros', function () {
   Manager::macro('foo', function(string $name) {
      $resolver = new class ($name) implements ResolverInterface {
            public function __construct(public string $name) {}
            public function toLinks(): array { return []; }
      };
      $this->register('test.'.$name, $resolver);
      return $resolver;
   });

   $service = new Manager();
   $service->foo('bar');

   $resolver = $service->for('test.bar');

   expect($resolver)->toBeInstanceOf(ResolverInterface::class);
   expect($resolver->name)->toBe('bar');
});

it('can return concrete links list', function () {
   $service = new Manager();
   $service->route('home')->title('Homepage');
   $service->route('foo')->title(fn() => date('c'));
   $service->route('bar');

   $links = $service->links()->all();
   expect($links)->toHaveCount(3);
   expect($links[0]->getResolverKey())->toBe('home');
   expect($links[0]->getTitle())->toBe('Homepage');
   expect($links[1]->getResolverKey())->toBe('foo');
   expect($links[1]->getTitle())->toBe(date('c'));
   expect($links[2]->getResolverKey())->toBe('bar');
   expect($links[2]->getTitle())->toBe('bar');
});

it('can return concrete links list without unwanted resolvers', function () {
   $service = new Manager();
   $service->route('home')->title('Homepage');
   $service->route('foo')->title(fn() => date('c'));
   $service->route('bar');

   $withoutHome = $service->links()->except('home')->all();
   expect($withoutHome)->toHaveCount(2);
   expect($withoutHome[0]->getResolverKey())->toBe('foo');
   expect($withoutHome[1]->getResolverKey())->toBe('bar');

   $withoutFooBar = $service->links()->except(['foo','bar'])->all();
   expect($withoutFooBar)->toHaveCount(1);
   expect($withoutFooBar[0]->getResolverKey())->toBe('home');
});

it('can return concrete links list of selected resolvers ', function () {
   $service = new Manager();
   $service->route('home')->title('Homepage');
   $service->route('foo')->title(fn() => date('c'));
   $service->route('bar');

   $onlyHome = $service->links()->only('home')->all();
   expect($onlyHome)->toHaveCount(1);
   expect($onlyHome[0]->getResolverKey())->toBe('home');

   $onlyFooBar = $service->links()->only(['foo','bar'])->all();
   expect($onlyFooBar)->toHaveCount(2);
   expect($onlyFooBar[0]->getResolverKey())->toBe('foo');
   expect($onlyFooBar[1]->getResolverKey())->toBe('bar');
});
