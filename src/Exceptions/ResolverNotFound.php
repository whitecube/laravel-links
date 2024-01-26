<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class ResolverNotFound extends InvalidArgumentException implements ResolvingException
{
    use ReportsResolver;
    
    /**
     * Create a new Exception for undefined resolver keys.
     */
    public static function forKey(string $key): static
    {
        return new self(sprintf('Link resolver for key "%s" is undefined.', $key));
    }
}
