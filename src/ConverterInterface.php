<?php

namespace Whitecube\Links;

interface ConverterInterface
{
    /**
     * Run the conversion process and return the result.
     */
    public function resolve(): mixed;

    /**
     * Get the encountered resolving exceptions.
     */
    public function getExceptions(): array;
}
