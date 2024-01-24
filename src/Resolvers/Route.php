<?php

namespace Whitecube\Links\Resolvers;

use Whitecube\Links\Link;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\ResolverInterface;

class Route implements ResolverInterface
{
    use Concerns\ResolvesRoutes;
    use Concerns\HasOption;

    /**
     * The resolver's identification key.
     */
    public readonly string $key;

    /**
     * Create a new simple Route Resolver.
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
        return new Link(
            url: $this->generateUrl($value['data'] ?? []),
            arguments: $value['data'] ?? [],
            resolver: $this,
        );
    }

    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection
    {
        return $this->getOptionInstance()->title($this->getTitle());
    }
}
