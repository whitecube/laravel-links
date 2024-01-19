<?php

namespace Whitecube\Links;

class ResolverRepository
{
    /**
     * The registered URL resolvers.
     */
    protected array $items = [];

    /**
     * Add a new URL resolver to the repository.
     */
    public function register(ResolverInterface $resolver): void
    {
        $this->items[] = $resolver;
    }

    /**
     * Retrieve a registered URL resolver by its key.
     */
    public function match(string $key): ?ResolverInterface
    {
        foreach($this->items as $resolver) {
            if(! ($instance = $resolver->for($key))) continue;
            return $instance;
        }

        return null;
    }

    /**
     * Return all registered URL resolver.
     */
    public function all(): array
    {
        return $this->items;
    }
}
