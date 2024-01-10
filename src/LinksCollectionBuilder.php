<?php

namespace Whitecube\Links;

use Illuminate\Support\Collection;

class LinksCollectionBuilder
{
    /**
     * The URL resolvers that will be transformed into Links.
     */
    protected array $resolvers = [];

    /**
     * Create a new collection builder.
     */
    public function __construct(ResolverRepository $repository)
    {
        $this->resolvers = $repository->all();
    }

    /**
     * Remove one or more resolvers from the collection
     */
    public function except(string|array $keys): static
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        foreach($keys as $key) {
            if (! isset($this->resolvers[$key])) continue;
            unset($this->resolvers[$key]);
        }

        return $this;
    }

    /**
     * Transform the selected resolvers into an array of concrete Links.
     */
    public function toArray(): array
    {
        return array_reduce(
            $this->resolvers,
            fn($links, $resolver) => array_merge($links, array_values($resolver->toLinks())),
            []
        );
    }

    /**
     * Alias of toArray.
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * Transform the selected resolvers into a collection of concrete Links.
     */
    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }

    /**
     * Alias of toCollection.
     */
    public function get(): Collection
    {
        return $this->toCollection();
    }
}
