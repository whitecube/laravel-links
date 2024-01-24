<?php

namespace Whitecube\Links;

class OptionsCollectionBuilder
{
    /**
     * The URL resolvers that will be transformed into Links.
     */
    protected array $resolvers = [];

    /**
     * Create a new collection builder.
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Return a new collection builder without the requested resolvers.
     */
    public function except(string|array $keys): static
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        $keep = [];

        foreach($this->resolvers as $resolver) {
            if(in_array($resolver->key, $keys)) continue;
            $keep[] = $resolver;
        }

        return new static($keep);
    }

    /**
     * Return a new collection builder with the requested resolvers.
     */
    public function only(string|array $keys): static
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        $keep = [];

        foreach($this->resolvers as $resolver) {
            if(! in_array($resolver->key, $keys)) continue;
            $keep[] = $resolver;
        }

        return new static($keep);
    }

    /**
     * Transform the selected resolvers into an array of concrete Links.
     */
    public function toArray(): array
    {
        return array_values(array_filter(
            array_map(fn($resolver) => $resolver->toOption(), $this->resolvers)
        ));
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
    public function toCollection(): OptionsCollection
    {
        return new OptionsCollection($this->toArray());
    }

    /**
     * Alias of toCollection.
     */
    public function get(): OptionsCollection
    {
        return $this->toCollection();
    }
}
