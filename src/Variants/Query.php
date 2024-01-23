<?php

namespace Whitecube\Links\Variants;

use Closure;
use Whitecube\Links\Variant;
use Whitecube\Links\VariantsRepositoryInterface;
use Illuminate\Contracts\Database\Query\Builder;

class Query implements VariantsRepositoryInterface
{
    /**
     * The defined variants.
     */
    protected Builder $query;

    /**
     * The variant "key" attribute or closure.
     */
    protected null|string|Closure $key = null;

    /**
     * The previously queried variants.
     */
    protected ?array $cache = null;

    /**
     * Create a new variants repository instance.
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Set the "key" attribute or closure capable of identifying the variants
     */
    public function keyBy(string|Closure $attribute): void
    {
        $this->key = $attribute;
    }

    /**
     * Return the prepared query that should be executed.
     */
    protected function getExecutableQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Return an array of Variant instances representing the provided raw data.
     */
    protected function toVariants(array $results): array
    {
        return array_values(array_map(
            fn($index, $item) => new Variant($item, $this->getVariantKey($item, $index)),
            array_keys($results),
            array_values($results)
        ));
    }

    /**
     * Return an unique identifying key for the provided item.
     */
    protected function getVariantKey(mixed $item, int|string $index): int|string
    {
        if(! $this->key) {
            return $index;
        }

        if(is_a($this->key, Closure::class)) {
            return call_user_func($this->key, $item);
        }

        return data_get($item, $this->key);
    }

    /**
     * Return all available variants.
     */
    public function all(): array
    {
        if(! is_null($this->cache)) {
            return $this->cache;
        }

        return $this->cache = $this->toVariants(
            $this->getExecutableQuery()->get()->all()
        );
    }
}
