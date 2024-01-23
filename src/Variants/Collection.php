<?php

namespace Whitecube\Links\Variants;

use Closure;
use Whitecube\Links\Variant;
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
        $this->items = array_map(fn($key, $value) => new Variant($value, $key), array_keys($items), array_values($items));
    }

    /**
     * Set the "key" attribute or closure capable of identifying the variants
     */
    public function keyBy(string|Closure $attribute): void
    {
        $this->items = array_map(function($variant) use ($attribute) {
            $data = $variant->raw();
            $key = is_a($attribute, Closure::class) ? call_user_func($attribute, $data) : $variant->$attribute;
            return new Variant($data, $key);
        }, $this->items);
    }

    /**
     * Return all available variants.
     */
    public function all(): array
    {
        return $this->items;
    }
}
