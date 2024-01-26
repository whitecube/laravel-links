<?php

namespace Whitecube\Links\Converters;

use Whitecube\Links\Link;
use Whitecube\Links\ConverterInterface;
use Whitecube\Links\Exceptions\ResolvingException;

class InlineTags implements ConverterInterface
{
    /**
     * The initial raw string to convert.
     */
    protected string $value;

    /**
     * The found inline tags with their resolved values.
     */
    protected array $tags = [];

    /**
     * Create a new converter instance.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Run the conversion process and return the result.
     */
    public function resolve(): mixed
    {
        $this->tags = $this->extractTags();

        $this->resolveTags();

        return $this->replaceTags();
    }

    /**
     * Get the encountered resolving exceptions.
     */
    public function getExceptions(): array
    {
        return array_values(array_filter(
            $this->tags,
            fn($result) => is_a($result, ResolvingException::class)
        ));
    }

    /**
     * Extract all the link references.
     */
    protected function extractTags(): array
    {
        preg_match_all('/(\\#link\\[(?:.*?)\\])/', $this->value, $matches);

        $tags = array_values(array_unique($matches[0] ?? []));

        return array_combine($tags, array_pad([], count($tags), null));
    }

    /**
     * Get a resolved link instance for each extracted tag.
     */
    protected function resolveTags(): void
    {
        foreach ($this->tags as $tag => $placeholder) {
            $this->tags[$tag] = $this->getResolvedTag($tag);
        }
    }

    /**
     * Try to resolve provided tag and return the process result.
     */
    protected function getResolvedTag(string $tag): Link|ResolvingException
    {
        try {
            return Link::fromInlineTag($tag);
        } catch (ResolvingException $e) {
            return $e;
        }
    }

    /**
     * Try to resolve provided tag and return the process result.
     */
    protected function replaceTags(): string
    {
        $search = array_keys($this->tags);
        $replace = array_map(function($result) {
            if(is_a($result, Link::class)) {
                return $result->url;
            }

            // TODO : return a developer-defined default value ?
            return '#';
        }, array_values($this->tags));

        return str_replace($search, $replace, $this->value);
    }
}
