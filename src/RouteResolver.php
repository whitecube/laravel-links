<?php

namespace Whitecube\Links;

class RouteResolver implements ResolverInterface
{
    use Concerns\ResolvesRoutes;

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
     * Transform the resolver into an array of concrete Links.
     */
    public function toLinks(): array
    {
        return [
            Link::make($this->key)->arguments($this->getRouteArguments())
        ];
    }
}
