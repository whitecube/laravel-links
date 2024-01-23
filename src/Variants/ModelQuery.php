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

    /**
     * Return the prepared query that should be executed.
     */
    protected function getExecutableQuery(): Builder
    {
        if($this->queryCallback) {
            call_user_func($this->queryCallback, $this->query);
            // once executed, we do not need to keep the callback in order to prevent
            // to execute it again next time the query needs to be executed.
            $this->queryCallback = null;
        }

        return $this->query;
    }
}
