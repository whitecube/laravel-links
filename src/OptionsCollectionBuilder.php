<?php

namespace Whitecube\Links;

use Illuminate\Support\Collection;

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

        foreach($this->resolvers as $key => $resolver) {
            if(in_array($key, $keys)) continue;
            $keep[$key] = $resolver;
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

        foreach($this->resolvers as $key => $resolver) {
            if(! in_array($key, $keys)) continue;
            $keep[$key] = $resolver;
        }

        return new static($keep);
    }

    /**
     * Transform the selected resolvers into an array of concrete Links.
     */
    public function toArray(): array
    {
        return array_values(array_filter(
            array_map(fn($resolver) => $resolver->toOption(), $this->resolvers),
            fn(?OptionInterface $option) => ($option && $option->isAvailable())
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
