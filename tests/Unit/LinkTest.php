<?php

use Whitecube\Links\Link;

it('can serialize to array', function () {
    $link = new Link('foo','bar');
    $link->title('This is a testing title');
    $link->arguments(['test' => 1, 'value' => 'something']);

    $result = $link->toArray();

    expect($result)->toBeArray();
    expect($result)->toMatchArray([
        'key' => 'foo',
        'id' => 'bar',
        'test' => '1',
        'value' => 'something'
    ]);
});

it('can serialize to inline tag', function () {
    $link = new Link('foo','bar');
    $link->title('This is a testing title');
    $link->arguments(['test' => 1, 'value' => 'something']);

    $result = $link->toInlineTag();

    expect($result)->toBe('@link(key:foo,id:bar,test:1,value:something)');
});