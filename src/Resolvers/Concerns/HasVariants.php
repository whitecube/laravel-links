<?php

namespace Whitecube\Links\Resolvers\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Database\Query\Builder;
use Whitecube\Links\VariantsRepositoryInterface;
use Whitecube\Links\Variants\Query as VariantsQuery;
use Whitecube\Links\Variants\Collection as VariantsCollection;
use Whitecube\Links\Variants\ModelQuery as VariantsModelQuery;
use Whitecube\Links\Resolvers\IndexRoute;
use Whitecube\Links\Exceptions\InvalidArgument;

trait HasVariants
{
    /**
     * The defined variants collection/repository.
     */
    protected ?VariantsRepositoryInterface $variants = null;

    /**
     * Define an array/collection as available variants.
     */
    public function collect(array|Collection|Arrayable|Closure $items): static
    {
        if(is_a($items, Closure::class)) {
            $items = call_user_func($items);
        }

        if(is_a($items, Collection::class)) {
            $items = $items->all();
        }

        if(is_a($items, Arrayable::class)) {
            $items = $items->toArray();
        }

        if(! is_array($items)) {
            throw InvalidArgument::for()
                ->method(static::class, 'collect')
                ->argument('items')
                ->expected(['array', Collection::class, Arrayable::class])
                ->received($items);
        }

        $this->variants = new VariantsCollection($items);

        return $this;
    }

    /**
     * Define a raw database Query Builder as variants supplier.
     */
    public function query(Builder|Closure $builder): static
    {
        if(is_a($builder, Closure::class)) {
            $builder = call_user_func($builder);
        }

        if(! is_a($builder, Builder::class)) {
            throw InvalidArgument::for()
                ->method(static::class, 'query')
                ->argument('builder')
                ->expected(Builder::class)
                ->received($builder);
        }

        $this->variants = new VariantsQuery($builder);

        return $this;
    }

    /**
     * Define a Model Query Builder as variants supplier.
     */
    public function model(string $classname, ?Closure $queryCallback = null): static
    {
        if(! is_a($classname, Model::class, true)) {
            throw InvalidArgument::for()
                ->method(static::class, 'model')
                ->argument('classname')
                ->expected(Model::class)
                ->received($classname);
        }

        $this->variants = new VariantsModelQuery($classname, $queryCallback);

        return $this;
    }
}
