<?php

namespace Whitecube\Links\Resolvers;

use Whitecube\Links\Link;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\ResolverInterface;

class ArchiveIndexRoute implements ResolverInterface
{
    use Concerns\ResolvesRoutes;
    use Concerns\HasOption;

    /**
     * The resolver's identification key.
     */
    public readonly string $key;

    /**
     * Create a new archive index Route Resolver.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Check if this resolver is suited for the provided key and return
     * itself or a more appropriate/specific resolver if available.
     */
    public function for(string $key): ?ResolverInterface
    {
        return ($this->key === $key) ? $this : null;
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
        return $this->getOptionInstance()->title($this->getTitle());
    }
}
