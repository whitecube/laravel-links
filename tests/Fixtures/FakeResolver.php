<?php

namespace Whitecube\Links\Tests\Fixtures;

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
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection
    {
        return null;
    }

    /**
     * Generate the effective URL.
     */
    public function resolve(array $arguments = []): string
    {
        return '#';
    }
}
