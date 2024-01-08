<?php

namespace Whitecube\Links;

use Whitecube\Links\Exceptions\ResolverNotFound;

class Manager
{
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
        $resolver = (new RouteResolver())->route($name, $arguments);

        $this->register('route.'.$name, $resolver);

        return $resolver;
    }

    /**
     * Register a named URL resolver instance in the link resolvers repository.
     */
    public function register(string $key, ResolverInterface $resolver): void
    {
        $this->repository->register($key, $resolver);
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
        return $this->repository->get($key);
    }
}
