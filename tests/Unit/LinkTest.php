<?php

use Whitecube\Links\Link;
use Whitecube\Links\Manager;
use Whitecube\Links\Exceptions\VariantNotFound;
use Whitecube\Links\Exceptions\InvalidSerializedValue;
use Whitecube\Links\Tests\Fixtures\FakeModel;

expect()->extend('toBeWorkingLinkInstanceFor', function ($format, $target) {
    $this->toBeInstanceOf(Link::class);
    expect(strval($this->value))->toBe('https://foo.bar/testing-route');

    if($format === 'array') {
        $serialized = $this->value->toArray();
        expect($serialized)->toBeArray();
        expect($serialized)->toMatchArray($target);
    } else if ($format === 'tag') {
        $tag = $link->toInlineTag();
        expect($tag)->toBe($target);
    }
});

it('cannot resolve array with missing resolver', function () {
    $service = setupAppBindings();

    $service->route('foo')->title('bar');

    $data = [
        'key' => 'foo',
    ];

    Link::fromArray($data);
})->throws(InvalidSerializedValue::class, 'Provided serialized link value should have a "resolver" attribute.');

it('can hydrate from array and serialize to array using route resolver', function () {
    $service = setupAppBindings();
    setupRoute('foo');

    $service->route('foo')->title('Bar');

    $data = [
        'resolver' => 'foo',
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeWorkingLinkInstanceFor('array', $data);
    expect($link->getTitle())->toBe('Bar');
});

it('cannot resolve archive resolver directly', function () {
    $service = setupAppBindings();

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('bar'))
        ->items(fn($entry) => $entry->route('bar')->collect([]));

    $data = [
        'resolver' => 'foo',
    ];

    Link::fromArray($data);
})->throws(InvalidSerializedValue::class, 'Provided serialized link value is targetting an ambiguous archive resolver (available: "foo.index", "foo.item", got "foo").');

it('can hydrate from array and serialize to array using archive index resolver', function () {
    $service = setupAppBindings();
    setupRoute('index');

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index')->title('Foo index'))
        ->items(fn($entry) => $entry->route('bar')->collect([]));

    $data = [
        'resolver' => 'foo.index',
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeWorkingLinkInstanceFor('array', $data);
    expect($link->getTitle())->toBe('Foo index');
});

it('cannot resolve array for archive item resolver without variant key', function () {
    $service = setupAppBindings();

    $service->archive('foo')->items(fn($entry) => $entry->route('bar')->collect([]));

    $data = [
        'resolver' => 'foo.item',
    ];

    Link::fromArray($data);
})->throws(InvalidSerializedValue::class, 'Provided serialized link value for archive item resolver should have a "variant" attribute.');

it('cannot resolve array for archive item resolver with unknown variant key', function () {
    $service = setupAppBindings();

    $service->archive('foo')->items(fn($entry) => $entry->route('bar')->collect([]));

    $data = [
        'resolver' => 'foo.item',
        'variant' => 'test',
    ];

    Link::fromArray($data);
})->throws(VariantNotFound::class, 'Variant for key "test" could not be found.');

it('can hydrate from array and serialize to array using archive item resolver with variant key', function () {
    $service = setupAppBindings();
    setupRoute('item', ['slug' => 'two']);

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(function($entry) {
            $entry->route('item')
                ->collect(FakeModel::$items)
                ->keyBy('id')
                ->parameter('slug', fn($variant) => $variant->slug)
                ->title(fn($variant) => $variant->title);
        });

    $data = [
        'resolver' => 'foo.item',
        'variant' => '2',
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeWorkingLinkInstanceFor('array', $data);
    expect($link->getTitle())->toBe('Post Two');
});

it('can hydrate from array and serialize to array using resolver key plus extra arguments', function () {
    setupAppBindings();

    $data = [
        'resolver' => 'foo',
        'data' => [
            'test' => 'something',
        ],
    ];

    expect(Link::fromArray($data))->toBeWorkingLinkInstanceFor('array', $data);
});

it('can hydrate from array and serialize to array using resolver and variant keys plus extra arguments', function () {
    setupAppBindings();
    
    $data = [
        'resolver' => 'foo.item',
        'variant' => 'bar',
        'data' => [
            'test' => 'something',
        ],
    ];

    expect(Link::fromArray($data))->toBeWorkingLinkInstanceFor('array', $data);
});

it('can hydrate from inline tag and serialize to inline tag', function () {
    setupAppBindings();
    
    $tag = "@link('foo@bar',['test'=>'something'])";

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can define a displayable title', function () {
    $link = new Link('foo','bar');

    expect($link->title('This is a testing title'))->toBe($link);
    expect($link->getTitle())->toBe('This is a testing title');

    expect($link->toArray()['title'] ?? null)->toBeNull();
});
