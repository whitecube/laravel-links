<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class VariantNotFound extends InvalidArgumentException implements ResolvingException
{
    use ReportsResolver;

    /**
     * Create a new Exception for undefined variant keys.
     */
    public static function forKey(string $key): static
    {
        return new self(sprintf('Variant for key "%s" could not be found.', $key));
    }
}
