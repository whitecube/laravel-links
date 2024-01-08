<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class ResolverNotFound extends InvalidArgumentException
{
    /**
     * Create a new Exception for undefined resolver keys.
     */
    public static function forKey(string $key): never
    {
        throw new self(sprintf('Link resolver for key "%s" is undefined.', $key));
    }
}
