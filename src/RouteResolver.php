<?php

namespace Whitecube\Links;


class RouteResolver implements ResolverInterface
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
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): ?OptionInterface
    {
        return $this->getOptionInstance()
            ->title($this->getTitle());
    }
}
