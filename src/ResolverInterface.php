<?php

namespace Whitecube\Links;

interface ResolverInterface
{
    /**
     * Transform the resolver into an array of concrete Links.
     */
    public function toLinks(): array;
}