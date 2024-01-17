<?php

namespace Whitecube\Links\Tests\Fixtures;

use Whitecube\Links\ResolverInterface;

class FakeResolver implements ResolverInterface
{
    /**
     * Transform the resolver into an array of concrete Links.
     */
    public function toLinks(): array
    {
        return [];
    }
}
