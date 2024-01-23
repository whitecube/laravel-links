<?php

namespace Whitecube\Links;

interface VariantsRepositoryInterface
{
    /**
     * Return all available variants.
     */
    public function all(): array;
}
