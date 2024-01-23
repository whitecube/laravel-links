<?php

namespace Whitecube\Links;

interface ResolverInterface
{
    /**
     * Check if this resolver is suited for the provided key and return
     * itself or a more appropriate/specific resolver if available.
     */
    public function for(string $key): ?ResolverInterface;
    
    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection;

    /**
     * Generate the effective URL.
     */
    public function resolve(array $arguments = []): string;
}