<?php

use Whitecube\Links\Manager;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Facades\Links;

it('transfers method calls to the links manager', function() {
    Links::route('foo')->title('bar');

    $resolver = app(Manager::class)->for('foo');

    expect($resolver)->toBeInstanceOf(ResolverInterface::class);
})->only();
