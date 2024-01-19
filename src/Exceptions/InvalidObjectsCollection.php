<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class InvalidObjectsCollection extends InvalidArgumentException
{
    /**
     * Create a new Exception for an invalid serialized array value.
     */
    public static function forValue(mixed $value): never
    {
        $type = gettype($value);

        if($type === 'object') {
            $type = 'instance of "'.get_class($value).'"';
        } else {
            $type = '"'.$type.'"';
        }

        throw new self(sprintf('Link resolver items should be of type "array", "Illuminate\\Contracts\\Database\\Query\\Builder" or "Illuminate\\Support\\Collection", got %s.', $type));
    }
}
