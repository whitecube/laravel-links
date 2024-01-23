<?php

namespace Whitecube\Links\Variants;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;

class ModelQuery extends Query
{
    /**
     * The model's classname.
     */
    protected string $classname;

    /**
     * The query callback.
     */
    protected ?Closure $queryCallback;

    /**
     * Create a new variants repository instance.
     */
    public function __construct(string $classname, ?Closure $queryCallback)
    {
        $this->classname = $classname;
        $this->query = $classname::query();
        $this->queryCallback = $queryCallback;
    }
}
