<?php

namespace Whitecube\Links;

use Closure;

interface VariantsRepositoryInterface
{
    /**
     * Set the "key" attribute or closure capable of identifying the variants
     */
    public function keyBy(string|Closure $attribute): void;
    
    /**
     * Return all available variants.
     */
    public function all(): array;
    
    /**
     * Return a specific variant matching provided key.
     */
    public function find(int|string $key): ?Variant;
}
