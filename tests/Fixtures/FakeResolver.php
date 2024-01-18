<?php

namespace Whitecube\Links\Tests\Fixtures;

use Whitecube\Links\OptionInterface;
use Whitecube\Links\ResolverInterface;

class FakeResolver implements ResolverInterface
{
    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): ?OptionInterface
    {
        return null;
    }
}
