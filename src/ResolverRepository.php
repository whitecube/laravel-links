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
    public function register(string $key, ResolverInterface $resolver): void
    {
        $this->items[$key] = $resolver;
    }

    /**
     * Retrieve a registered URL resolver by its key.
     */
    public function get(string $key): ?ResolverInterface
    {
        return $this->items[$key] ?? null;
    }
}
