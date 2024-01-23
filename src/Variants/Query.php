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
     * Create a new variants repository instance.
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }
}
