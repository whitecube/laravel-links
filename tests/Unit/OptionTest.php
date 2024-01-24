<?php

use Whitecube\Links\Option;
use Whitecube\Links\OptionPanel;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;

it('can be instantiated with resolver & object keys', function () {
    $option = new Option('foo','bar');

    expect($option->getResolverKey())->toBe('foo');
    expect($option->getVariantKey())->toBe('bar');
    expect($option->getKey())->toBe('foo@bar');
});

it('can be instantiated with resolver key but without object key', function () {
    $option = new Option('foo');

    expect($option->getResolverKey())->toBe('foo');
    expect($option->getVariantKey())->toBeNull();
    expect($option->getKey())->toBe('foo');
});

it('can define a displayable title', function () {
    $option = new Option('foo','bar');

    expect($option->getTitle())->toBe('foo@bar');
    expect($option->title(''))->toBe($option);
    expect($option->getTitle())->toBe('foo@bar');

    $option->title('This is a testing title');
    expect($option->getTitle())->toBe('This is a testing title');
});

it('can define extra arguments and keep null arguments', function () {
    $option = new Option('foo');

    expect($option->arguments(['test1' => 'value1', 'test2' => 'value2', 'test3' => null]))->toBe($option);
    expect($option->argument('test4', 'value4'))->toBe($option);
    $option->argument('test2', 'new2');
    $option->argument('test5', null);
    $option->argument('test6', 42);
    $option->arguments(['test4' => 'new4', 'test7' => 'value7']);

    expect($option->getArguments())->toMatchArray([
        'test1' => 'value1',
        'test2' => 'new2',
        'test3' => null,
        'test4' => 'new4',
        'test5' => null,
        'test6' => '42',
        'test7' => 'value7',
    ]);
});

it('can magically define and read extra arguments', function () {
    $option = new Option('foo');

    $option->test1 = 'value1';
    $option->test2 = 42;
    $option->test3 = new class() {
        public function __toString() { return 'value3'; }
    };

    expect($option->test1)->toBe('value1');
    expect($option->test2)->toBe('42');
    expect($option->test3)->toBe('value3');
});

it('can define and return sub-options', function () {
    $option = new Option('foo');

    $header = new Option('bar');
    $footer = new class() implements OptionInterface {
        public function getResolverKey(): string { return 'custom'; }
        public function getVariantKey(): null|int|string { return null; }
        public function title(string $title): static { return $this; }
        public function getTitle(): string { return 'custom-title'; }
        public function arguments(array $arguments): static { return $this; }
        public function argument(string $attribute, mixed $value): static { return $this; }
        public function getArguments(): array { return []; }
        public function hasChoices(): bool { return false; }
        public function choices(Closure $setup): static { return $this; }
        public function getChoices(): ?OptionPanel { return null; }
        public function jsonSerialize(): mixed { return []; }
    };

    expect($option->hasChoices())->toBeFalse();
    expect($option->getChoices())->toBeNull();
    expect($option->choices(fn(OptionPanel $panel) => $panel->prepend($header)->append($footer)))->toBe($option);
    expect($option->hasChoices())->toBeTrue();

    $panel = $option->getChoices();
    expect($panel)->toBeInstanceOf(OptionPanel::class);
    $serialized = $panel->options();
    expect($serialized)->toBeArray();
    expect($serialized)->toHaveCount(2);
    expect($serialized)->toMatchArray([$header, $footer]);

    $collection = new OptionsCollection;
    expect($panel->archive($collection))->toBe($panel);
    $serialized = $panel->options();
    expect($serialized)->toBeArray();
    expect($serialized)->toHaveCount(3);
    expect($serialized)->toMatchArray([$header, $collection, $footer]);
});
