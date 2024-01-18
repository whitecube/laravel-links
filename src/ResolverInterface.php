<?php

namespace Whitecube\Links;

interface ResolverInterface
{
    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): ?OptionInterface;
}