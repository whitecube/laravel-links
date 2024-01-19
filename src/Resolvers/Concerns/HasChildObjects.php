<?php

namespace Whitecube\Links\Resolvers\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Database\Query\Builder;
use Whitecube\Links\Resolvers\IndexRoute;
use Whitecube\Links\Exceptions\InvalidObjectsCollection;

trait HasChildObjects
{
    /**
     * The defined or queried declination objects.
     */
    protected ?array $items = null;

    /**
     * The queried model's classname.
     */
    protected ?Builder $query = null;

    /**
     * The queried model's classname.
     */
    protected ?string $model = null;

    /**
     * The eventual index/archive page URL configuration callback
     */
    protected null|Closure|IndexRoute $index = null;

    /**
     * Return all the defined URL resolver arguments.
     */
    public function items(array|Collection|Builder|Closure $items): static
    {
        if(is_a($items, Closure::class)) {
            $items = call_user_func($items);
        }

        if(is_a($items, Collection::class)) {
            $items = $items->all();
        }

        if(is_a($items, Builder::class)) {
            $this->query = $items;
            return $this;
        }

        if(! is_array($items)) {
            return InvalidObjectsCollection::forValue($items);
        }

        $this->items = $items;

        return $this;
    }

    /**
     * Return all the defined URL resolver arguments.
     */
    public function model(string $classname, ?Closure $builder = null): static
    {
        $this->model = $classname;

        $query = $classname::query();

        if($builder) {
            $result = call_user_func($builder, $query);
        }

        return $this->items($result ?? $query);
    }

    /**
     * Define a callback function for the resolver's "index"/"archive" URL.
     */
    public function index(Closure $callback): static
    {
        $this->index = $callback;

        return $this;
    }

    /**
     * Get the "index"/"archive" URL resolver.
     */
    protected function getIndexResolver(): ?IndexRoute
    {
        if(is_null($this->index) || is_a($this->index, IndexRoute::class)) {
            return $this->index;
        }

        $resolver = new IndexRoute();

        call_user_func($this->index, $resolver);

        return $this->index = $resolver;
    }
}