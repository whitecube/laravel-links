<?php

use Whitecube\Links\Converters\InlineTags;
use Whitecube\Links\Tests\Fixtures\FakeModel;

it('can convert all resolvable link tags in a string', function () {
    $service = setupAppBindings();
    setupRoute(name: 'foo', times: 3);

    $service->route('foo');
    $service->archive('posts')
        ->index(fn($entry) => $entry->route('foo'))
        ->items(fn($entry) => $entry->route('foo')->collect(FakeModel::$items)->keyBy('id'));

    $string = 'This is a markdown string containing a [route link](#link[foo]), an [archive index link](#link[posts.index]) and an [archive item link](#link[posts.item@2]).';
    $output = 'This is a markdown string containing a [route link](https://foo.bar/testing-route), an [archive index link](https://foo.bar/testing-route) and an [archive item link](https://foo.bar/testing-route).';

    $converter = new InlineTags($string);

    expect($converter->resolve())->toBe($output);
    expect($converter->getExceptions())->toBeEmpty();
});
