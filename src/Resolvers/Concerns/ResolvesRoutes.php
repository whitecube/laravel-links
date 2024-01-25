<?php

namespace Whitecube\Links\Resolvers\Concerns;

use Closure;
use Whitecube\Links\Variant;
use Illuminate\Support\Facades\URL;

trait ResolvesRoutes
{
    /**
     * The route's name for this URL.
     */
    protected string $routeName;

    /**
     * The route parameters for this URL.
     */
    protected array $routeParameterResolvers = [];

    /**
     * Set the URL resolver's route name and static parameters.
     */
    public function route(string $name, array $parameters = []): static
    {
        $this->routeName = $name;
        $this->routeParameterResolvers = $parameters;

        return $this;
    }

    /**
     * Add a route parameter resolver.
     */
    public function parameter(string $key, mixed $value): static
    {
        $this->routeParameterResolvers[$key] = $value;

        return $this;
    }

    /**
     * Get the URL resolver's route name.
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * Get the URL resolver's route parameters as defined.
     */
    public function getRawRouteParameterResolvers(): array
    {
        return $this->routeParameterResolvers;
    }

    /**
     * Get all the resolved route parameters.
     */
    public function getRouteParameters(?Variant $variant = null): array
    {
        return array_map(
            fn($parameter) => $this->resolveRouteParameter($parameter, $variant),
            $this->getRawRouteParameterResolvers()
        );
    }

    /**
     * Get a specific resolved route parameter by its key.
     */
    public function getRouteParameter(string $key, ?Variant $variant = null): mixed
    {
        $parameters = $this->getRawRouteParameterResolvers();

        if(! isset($parameters[$key])) {
            return null;
        }

        return $this->resolveRouteParameter($parameters[$key], $variant);
    }

    /**
     * Transform a raw route parameter definition into a usable route parameter value.
     */
    protected function resolveRouteParameter(mixed $parameter, ?Variant $variant = null): mixed
    {
        if(is_a($parameter, Closure::class)) {
            return call_user_func($parameter, $variant);
        }

        return $parameter;
    }

    /**
     * Generate the route's effective URL.
     */
    protected function generateUrl(?Variant $variant = null, array $parameters = []): string
    {
        return URL::route($this->getRouteName(), array_merge(
            $this->getRouteParameters($variant),
            $parameters
        ));
    }
}