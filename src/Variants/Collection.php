<?php

namespace Whitecube\Links\Variants;

use Whitecube\Links\VariantsRepositoryInterface;

class Collection implements VariantsRepositoryInterface
{
    /**
     * The defined variants.
     */
    protected array $items;

    /**
     * Create a new variants repository instance.
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Return all available variants.
     */
    public function all(): array
    {
        return $this->items;
    }
}
