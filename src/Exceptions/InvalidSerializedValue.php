<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class InvalidSerializedValue extends InvalidArgumentException
{
    /**
     * Create a new Exception for an invalid serialized array value.
     */
    public static function forArray(): never
    {
        throw new self('Provided serialized link array should at least contain a "key" index.');
    }
    /**
     * Create a new Exception for an invalid serialized inline tag value.
     */
    public static function forInlineTag(string $tag): never
    {
        throw new self(sprintf('Provided serialized inline tag should match syntax "@link(key:value)", got "%s"', $tag));
    }
}
