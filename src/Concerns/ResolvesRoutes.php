<?php

namespace Whitecube\Links\Concerns;

use Illuminate\Support\Facades\URL;

trait ResolvesRoutes
{
    /**
     * The route's name for this URL.
     */
    protected string $routeName;

    /**
     * The route's variables for this URL.
     */
    protected array $routeArguments = [];

    /**
     * Set the URL resolver's route name.
     */
    public function route(string $name, array $arguments = []): static
    {
        $this->routeName = $name;
        $this->routeArguments = $arguments;

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
     * Get the URL resolver's route name.
     */
    public function getRouteArguments(): array
    {
        return $this->routeArguments;
    }

    /**
     * Get the URL resolver's route name.
     */
    public function getRouteArgument(string $key): mixed
    {
        return $this->routeArguments[$key] ?? null;
    }

    /**
     * Get the URL resolver's route name.
     */
    public function resolve(array $arguments = []): string
    {
        return URL::route($this->getRouteName(), array_merge(
            $this->getRouteArguments(),
            $arguments
        ));
    }
}