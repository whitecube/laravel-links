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
     * Instantiate a Link object based on provided serialized value.
     */
    public function resolve(array $value, bool $silent): ?Link;
    
    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection;
}