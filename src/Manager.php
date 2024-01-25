<?php

namespace Whitecube\Links;

use Illuminate\Support\Traits\Macroable;
use Whitecube\Links\Resolvers\Route;
use Whitecube\Links\Resolvers\Archive;
use Whitecube\Links\Exceptions\ResolverNotFound;
use Whitecube\Links\Exceptions\InvalidSerializedValue;

class Manager
{
    use Macroable;
    
    /**
     * The registered URL resolvers.
     */
    protected ResolverRepository $repository;

    /**
     * Create a new Links Manager instance.
     */
    public function __construct()
    {
        $this->repository = new ResolverRepository();
    }

    /**
     * Register a new Route URL resolver.
     */
    public function route(string $name, array $arguments = []): ResolverInterface
    {
        return $this->register((new Route($name))->route($name, $arguments));
    }

    /**
     * Register a new resource Archive URLs resolver.
     */
    public function archive(string $key): ResolverInterface
    {
        return $this->register(new Archive($key));
    }

    /**
     * Register a named URL resolver instance in the link resolvers repository.
     */
    public function register(ResolverInterface $resolver): ResolverInterface
    {
        $this->repository->register($resolver);

        return $resolver;
    }

    /**
     * Transform a serialized link value into a proper resolved Link instance.
     */
    public function resolve(array $value, bool $silent = false): ?Link
    {
        if(! isset($value['resolver'])) {
            if($silent) return null;
            throw InvalidSerializedValue::missingResolver();
        }

        $resolver = ($silent)
            ? $this->tryFor($value['resolver'])
            : $this->for($value['resolver']);

        if(! $resolver) {
            return null;
        }

        return $resolver->resolve($value, $silent);
    }

    /**
     * Retrieve a registered resolver from the links resolvers repository
     * and throw an exception if not defined.
     */
    public function for(string $key): ResolverInterface
    {
        if(! ($resolver = $this->tryFor($key))) {
            return ResolverNotFound::forKey($key);
        }

        return $resolver;
    }

    /**
     * Retrieve a registered resolver from the links resolvers repository
     * or return null if not defined.
     */
    public function tryFor(string $key): ?ResolverInterface
    {
        return $this->repository->match($key);
    }

    /**
     * Return all the available Links.
     */
    public function options(): OptionsCollectionBuilder
    {
        return new OptionsCollectionBuilder($this->repository->all());
    }
}
