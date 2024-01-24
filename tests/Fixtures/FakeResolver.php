<?php

namespace Whitecube\Links\Tests\Fixtures;

use Whitecube\Links\Link;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\ResolverInterface;

class FakeResolver implements ResolverInterface
{
    public function __construct(protected string $key) {}

    /**
     * Check if this resolver is suited for the provided key and return
     * itself or a more appropriate/specific resolver if available.
     */
    public function for(string $key): ?ResolverInterface
    {
        return ($key === $this->key) ? $this : null;
    }

    /**
     * Instantiate a Link object based on provided serialized value.
     */
    public function resolve(array $value, bool $silent): ?Link
    {
        return null;
    }

    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection
    {
        return null;
    }
}
