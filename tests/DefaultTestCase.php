<?php

namespace Whitecube\Links\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class DefaultTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fixes Unit tests failing when running alon Feature tests:
        \Illuminate\Support\Facades\Facade::setFacadeApplication(null);
    }
}
