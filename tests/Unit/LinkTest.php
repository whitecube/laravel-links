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
    } else if ($format === 'key') {
        $key = $this->value->toKey();
        expect($key)->toBe($target);
    } else if ($format === 'tag') {
        $tag = $this->value->toInlineTag();
        expect($tag)->toBe($target);
    }
});

it('cannot resolve array with missing resolver', function () {
    $service = setupAppBindings();

    $service->route('foo');

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

it('can hydrate from array and serialize to array using route resolver plus extra arguments', function () {
    $service = setupAppBindings();
    setupRoute('foo', ['test' => 'something']);

    $service->route('foo');

    $data = [
        'resolver' => 'foo',
        'data' => [
            'test' => 'something',
        ],
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeWorkingLinkInstanceFor('array', $data);
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

it('can hydrate from array and serialize to array using archive item resolver with variant key plus extra arguments', function () {
    $service = setupAppBindings();
    setupRoute('item', ['slug' => 'three', 'test' => 'something']);

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(function($entry) {
            $entry->route('item')
                ->collect(FakeModel::$items)
                ->keyBy('id')
                ->parameter('slug', fn($variant) => $variant->slug);
        });
    
    $data = [
        'resolver' => 'foo.item',
        'variant' => '3',
        'data' => [
            'test' => 'something',
        ],
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeWorkingLinkInstanceFor('array', $data);
});

it('can hydrate from key and serialize to key using route resolver', function () {
    $service = setupAppBindings();
    setupRoute('foo');

    $service->route('foo');
    
    $key = 'foo';

    expect(Link::fromKey($key))->toBeWorkingLinkInstanceFor('key', $key);
});

it('can hydrate from key and serialize to key using archive index resolver', function () {
    $service = setupAppBindings();
    setupRoute('index');

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(fn($entry) => $entry->route('bar')->collect([]));
    
    $key = 'foo.index';

    expect(Link::fromKey($key))->toBeWorkingLinkInstanceFor('key', $key);
});

it('can hydrate from key and serialize to key using archive item resolver', function () {
    $service = setupAppBindings();
    setupRoute('item', ['slug' => 'one']);

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(function($entry) {
            $entry->route('item')
                ->collect(FakeModel::$items)
                ->keyBy('id')
                ->parameter('slug', fn($variant) => $variant->slug);
        });
    
    $key = 'foo.item@1';

    expect(Link::fromKey($key))->toBeWorkingLinkInstanceFor('key', $key);
});

it('can hydrate from inline tag and serialize to inline tag using route resolver', function () {
    $service = setupAppBindings();
    setupRoute('foo');

    $service->route('foo');
    
    $tag = '#link[foo]';

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can hydrate from inline tag and serialize to inline tag using route resolver plus extra arguments', function () {
    $service = setupAppBindings();
    setupRoute('foo', ['test' => 'true', 'value' => 'string: spaces, commas & other punctuation;']);

    $service->route('foo');
    
    $tag = '#link[foo,test:true,value:"string: spaces, commas & other punctuation;"]';

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can hydrate from inline tag and serialize to inline tag using archive index resolver', function () {
    $service = setupAppBindings();
    setupRoute('index');

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(fn($entry) => $entry->route('bar')->collect([]));
    
    $tag = '#link[foo.index]';

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can hydrate from inline tag and serialize to inline tag using archive item resolver', function () {
    $service = setupAppBindings();
    setupRoute('item', ['slug' => 'one']);

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(function($entry) {
            $entry->route('item')
                ->collect(FakeModel::$items)
                ->keyBy('id')
                ->parameter('slug', fn($variant) => $variant->slug);
        });
    
    $tag = '#link[foo.item@1]';

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can hydrate from inline tag and serialize to inline tag using archive item resolver plus extra arguments', function () {
    $service = setupAppBindings();
    setupRoute('item', ['slug' => 'one', 'test' => 'true', 'value' => 'string: spaces, commas & other punctuation;']);

    $service->archive('foo')
        ->index(fn($entry) => $entry->route('index'))
        ->items(function($entry) {
            $entry->route('item')
                ->collect(FakeModel::$items)
                ->keyBy('id')
                ->parameter('slug', fn($variant) => $variant->slug);
        });
    
    $tag = '#link[foo.item@1,test:true,value:"string: spaces, commas & other punctuation;"]';

    expect(Link::fromInlineTag($tag))->toBeWorkingLinkInstanceFor('tag', $tag);
});

it('can define a displayable title', function () {
    $link = new Link('foo','bar');

    expect($link->title('This is a testing title'))->toBe($link);
    expect($link->getTitle())->toBe('This is a testing title');

    expect($link->toArray()['title'] ?? null)->toBeNull();
});
