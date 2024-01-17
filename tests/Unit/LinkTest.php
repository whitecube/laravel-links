<?php

use Whitecube\Links\Link;

it('can hydrate from array and serialize to array', function () {
    $data = [
        'key' => 'bar',
        'id' => 'foo',
        'test' => 'something',
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeInstanceOf(Link::class);

    $result = $link->toArray();

    expect($result)->toBeArray();
    expect($result)->toMatchArray($data);
});

it('can hydrate from array without ID and serialize to array without ID', function () {
    $data = [
        'key' => 'bar',
        'test' => 'something',
    ];

    $link = Link::fromArray($data);

    expect($link)->toBeInstanceOf(Link::class);

    $result = $link->toArray();

    expect($result)->toBeArray();
    expect($result)->toMatchArray($data);
});

it('can hydrate from inline tag and serialize to inline tag', function () {
    $tag = '@link(key:foo,id:bar,test:1,value:something)';

    $link = Link::fromInlineTag($tag);

    expect($link)->toBeInstanceOf(Link::class);

    $result = $link->toInlineTag();

    expect($result)->toBe($tag);
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
