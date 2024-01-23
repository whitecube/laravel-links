<?php

namespace Whitecube\Links\Variants;

use Whitecube\Links\VariantsRepositoryInterface;
use Illuminate\Contracts\Database\Query\Builder;

class Query implements VariantsRepositoryInterface
{
    /**
     * The defined variants.
     */
    protected Builder $query;

    /**
     * The defined variants.
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
     * Return the prepared query that should be executed.
     */
    protected function getExecutableQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Return all available variants.
     */
    public function all(): array
    {
        if(! is_null($this->cache)) {
            return $this->cache;
        }

        return $this->cache = $this->getExecutableQuery()->get()->toArray();
    }
}
