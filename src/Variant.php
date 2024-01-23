<?php

namespace Whitecube\Links;

class Variant
{
    /**
     * The raw variant data.
     */
    protected mixed $data;

    /**
     * The variant's identifying key.
     */
    protected int|string $key;

    /**
     * Create a new variant instance.
     */
    public function __construct(mixed $data, int|string $key)
    {
        $this->data = $data;
        $this->key = $key;
    }

    /**
     * Create a new variant instance.
     */
    public function getKey(): int|string
    {
        return $this->key;
    }

    /**
     * Check if this variant is representing a data-structure such as an array or an object.
     */
    public function isStructure(): bool
    {
        return (is_array($this->data) || is_object($this->data));
    }

    /**
     * Get the variant's initial data.
     */
    public function raw(): mixed
    {
        return $this->data;
    }

    /**
     * Create a new variant instance.
     */
    public function __get(string $attribute): mixed
    {
        if(! $this->isStructure()) {
            return null;
        }

        return data_get($this->data, $attribute);
    }
}
