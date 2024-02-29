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
     * Return the prepared query that should be executed in order to list all variant results.
     */
    protected function getExecutableIndexQuery(): Builder
    {
        return clone $this->query;
    }

    /**
     * Return the prepared query that should be executed in order to list all variant results.
     */
    protected function getExecutableFindQuery(int|string $key): Builder
    {
        // TODO.
        // $column = $this->getKeyQueryColumn();
        // $key = $this->getKeyQueryValue($key);

        $query = clone $this->query;

        // $item = (is_string($this->key))
        //     ? $query->where($this->key, $key)->first()
        //     : $query->find($key);
        return $query->where('id',$key);
    }

    /**
     * Return an array of Variant instances representing the provided raw data.
     */
    protected function toVariants(array $results): array
    {
        return array_values(array_map(
            fn($index, $item) => $this->toVariant($item, $index),
            array_keys($results),
            array_values($results)
        ));
    }

    /**
     * Return a Variant instances representing the provided raw data.
     */
    protected function toVariant(mixed $item, int|string $index): Variant
    {
        return new Variant($item, $this->getVariantKey($item, $index));
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
            $this->getExecutableIndexQuery()->get()->all()
        );
    }

    /**
     * Return a specific variant matching provided key.
     */
    public function find(int|string $key): ?Variant
    {
        if(! is_null($this->cache)) {
            return $this->findCached($key);
        }

        $item = $this->getExecutableFindQuery($key)->first();

        if(! $item) {
            return null;
        }

        return $this->toVariant($item, $key);
    }

    /**
     * Return a specific cached variant by its key.
     */
    protected function findCached(int|string $key): ?Variant
    {
        foreach ($this->cache ?? [] as $variant) {
            if($variant->getKey() != $key) continue;
            return $variant;
        }

        return null;
    }
}
