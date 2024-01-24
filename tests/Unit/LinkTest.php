<?php

use Whitecube\Links\Link;
use Whitecube\Links\Manager;
use Whitecube\Links\Exceptions\InvalidSerializedValue;

expect()->extend('toBeWorkingLinkInstanceFor', function ($format, $target) {
    $this->toBeInstanceOf(Link::class);
    expect($this->value->getTitle())->toBe('Bar');

    if($format === 'array') {
        $serialized = $this->value->toArray();
        expect($serialized)->toBeArray();
        expect($serialized)->toMatchArray($target);
    } else if ($format === 'tag') {
        $tag = $link->toInlineTag();
        expect($tag)->toBe($target);
    }
});

it('can hydrate from array and serialize to array only using resolver key', function () {
    $service = setupAppBindings();
    setupRoute('foo');

    $service->route('foo')->title('Bar');

    $data = [
        'resolver' => 'foo',
    ];

    expect(Link::fromArray($data))->toBeWorkingLinkInstanceFor('array', $data);
})->only();

it('cannot resolve array with missing resolver', function () {
    $service = setupAppBindings();

    $service->route('foo')->title('bar');

    $data = [
        'key' => 'foo',
    ];

    Link::fromArray($data);
})->throws(InvalidSerializedValue::class, 'Provided serialized link value should at least contain a "resolver" attribute.');

it('can hydrate from array and serialize to array using resolver and variant keys', function () {
    setupAppBindings();

    $data = [
        'resolver' => 'foo.item',
        'variant' => 'bar',
    ];

    expect(Link::fromArray($data))->toBeWorkingLinkInstanceFor('array', $data);
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

it('can define extra arguments and remove null arguments', function () {
    $link = new Link('foo','bar');

    expect($link->arguments(['test1' => 'value1', 'test2' => 'value2', 'test3' => null]))->toBe($link);
    expect($link->argument('test4', 'value4'))->toBe($link);
    $link->argument('test2', 'new2');
    $link->argument('test5', null);
    $link->argument('test6', 'value6');
    $link->arguments(['test4' => 'new4', 'test7' => 'value7']);

    expect($link->toArray())->toMatchArray([
        'key' => 'foo',
        'id' => 'bar',
        'test1' => 'value1',
        'test2' => 'new2',
        'test4' => 'new4',
        'test6' => 'value6',
        'test7' => 'value7',
    ]);
});

it('can define a displayable title', function () {
    $link = new Link('foo','bar');

    expect($link->title('This is a testing title'))->toBe($link);
    expect($link->getTitle())->toBe('This is a testing title');

    expect($link->toArray()['title'] ?? null)->toBeNull();
});
