<?php

use Whitecube\Links\Exceptions\InvalidArgument;

it('can generate message from nothing', function () {
    expect(InvalidArgument::for()->getMessage())->toBe('Invalid value.');
});

it('can generate message from method only', function () {
    $instance = InvalidArgument::for()->method('MyFooClass','bar');

    expect($instance->getMessage())->toBe('Method MyFooClass::bar() was invoked with an invalid value.');
});

it('can generate message from argument name only', function () {
    $instance = InvalidArgument::for()->argument('foo');

    expect($instance->getMessage())->toBe('Argument "foo" was invoked with an invalid value.');
});

it('can generate message from method with argument', function () {
    $instance = InvalidArgument::for()->argument('foo')->method('MyFooClass','bar');

    expect($instance->getMessage())->toBe('Argument "foo" of method MyFooClass::bar() was invoked with an invalid value.');
});

it('can generate message from expectation only', function () {
    $instance = InvalidArgument::for()->expected('string');
    expect($instance->getMessage())->toBe('Expected value of type string.');
    $instance = InvalidArgument::for()->expected(['string','array']);
    expect($instance->getMessage())->toBe('Expected value of type string or array.');
    $instance = InvalidArgument::for()->expected(['string','array', InvalidArgument::class]);
    expect($instance->getMessage())->toBe('Expected value of type string, array or Whitecube\Links\Exceptions\InvalidArgument.');    
});

it('can generate message from subject with expectation', function () {
    $instance = InvalidArgument::for()->method('MyFooClass','bar')->expected('string');
    expect($instance->getMessage())->toBe('Method MyFooClass::bar() expected value of type string.');
    $instance = InvalidArgument::for()->argument('foo')->expected(['string','array']);
    expect($instance->getMessage())->toBe('Argument "foo" expected value of type string or array.');
    $instance = InvalidArgument::for()->argument('foo')->method('MyFooClass','bar')->expected(['string','array', InvalidArgument::class]);
    expect($instance->getMessage())->toBe('Argument "foo" of method MyFooClass::bar() expected value of type string, array or Whitecube\Links\Exceptions\InvalidArgument.');
});

it('can generate message from reception only', function () {
    $instance = InvalidArgument::for()->received('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
    expect($instance->getMessage())->toBe('Invalid value of type string "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do …".');
    $instance = InvalidArgument::for()->received('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do.');
    expect($instance->getMessage())->toBe('Invalid value of type string "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do.".');
    $instance = InvalidArgument::for()->received(1024);
    expect($instance->getMessage())->toBe('Invalid value of type integer "1024".');
    $instance = InvalidArgument::for()->received(1024.64);
    expect($instance->getMessage())->toBe('Invalid value of type double "1024.64".');
    $instance = InvalidArgument::for()->received(true);
    expect($instance->getMessage())->toBe('Invalid value of type bool TRUE.');
    $instance = InvalidArgument::for()->received(false);
    expect($instance->getMessage())->toBe('Invalid value of type bool FALSE.');
    $instance = InvalidArgument::for()->received(['foo' => 'bar', 'long' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
    expect($instance->getMessage())->toBe('Invalid value of type array "{"foo":"bar","long":"Lorem ipsum dolor sit amet, consectetur adi…".');
    $instance = InvalidArgument::for()->received(['foo' => 'bar']);
    expect($instance->getMessage())->toBe('Invalid value of type array "{"foo":"bar"}".');
    $instance = InvalidArgument::for()->received(new InvalidArgument('test'));
    expect($instance->getMessage())->toBe('Invalid value of type class instance "Whitecube\Links\Exceptions\InvalidArgument".');
});

it('can generate message from subject with reception', function () {
    $instance = InvalidArgument::for()->method('MyFooClass','bar')->received('Lorem ipsum');
    expect($instance->getMessage())->toBe('Method MyFooClass::bar() was invoked with an invalid value, got string "Lorem ipsum".');
    $instance = InvalidArgument::for()->argument('foo')->received('Lorem ipsum');
    expect($instance->getMessage())->toBe('Argument "foo" was invoked with an invalid value, got string "Lorem ipsum".');
    $instance = InvalidArgument::for()->argument('foo')->method('MyFooClass','bar')->received('Lorem ipsum');
    expect($instance->getMessage())->toBe('Argument "foo" of method MyFooClass::bar() was invoked with an invalid value, got string "Lorem ipsum".');
});

it('can generate message from expectation with reception', function () {
    $instance = InvalidArgument::for()->expected(InvalidArgument::class)->received('Lorem ipsum');
    expect($instance->getMessage())->toBe('Expected value of type Whitecube\Links\Exceptions\InvalidArgument but got string "Lorem ipsum".');
});

it('can generate full message', function () {
    $instance = InvalidArgument::for()
        ->method('MyFooClass','bar')
        ->argument('foo')
        ->expected(InvalidArgument::class)
        ->received('Lorem ipsum');
    expect($instance->getMessage())->toBe('Argument "foo" of method MyFooClass::bar() expected value of type Whitecube\Links\Exceptions\InvalidArgument, got string "Lorem ipsum".');
});


