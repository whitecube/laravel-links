<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;
use Whitecube\Links\Resolvers\Archive;

class InvalidSerializedValue extends InvalidArgumentException
{
    /**
     * Create a new Exception for an invalid serialized inline tag value.
     */
    public static function inlineTagSyntax(string $tag): static
    {
        return new self(sprintf('Provided serialized inline tag should match syntax "@link(key:value)", got "%s"', $tag));
    }

    /**
     * Create a new Exception for an invalid serialized array value because of a missing "resolver" key.
     */
    public static function missingResolver(): static
    {
        return new self('Provided serialized link value should at least contain a "resolver" attribute.');
    }

    /**
     * Create a new Exception for an invalid serialized array value because of a missing "resolver" key.
     */
    public static function ambiguousArchiveResolver(Archive $archive, string $key): static
    {
        $available = implode(', ', array_map(fn($key) => '"'.$key.'"', array_filter([
            $archive->getIndex()?->key,
            $archive->getItems()?->key,
        ])));

        return new self(sprintf(
            'Provided serialized link value is targetting an ambiguous archive resolver (available: %s, got "%s").',
            $available, $key
        ));
    }
}
