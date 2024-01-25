<?php

namespace Whitecube\Links;

use Illuminate\Support\Facades\App;
use Whitecube\Links\Exceptions\InvalidSerializedValue;

class Link
{
    /**
     * The resolved URL.
     */
    public readonly string $url;

    /**
     * The eventual extra provided arguments.
     */
    protected array $data;

    /**
     * The defined title attribute for this link.
     */
    protected ?string $title;

    /**
     * The resolver used to create this link.
     */
    protected ResolverInterface $resolver;

    /**
     * The eventual variant represented by this link.
     */
    protected ?Variant $variant;

    /**
     * Create a new Link instance.
     */
    public function __construct(string $url, ResolverInterface $resolver, ?Variant $variant = null, array $data = [])
    {
        $this->url = $url;
        $this->resolver = $resolver;
        $this->variant = $variant;
        $this->data = $data;
    }

    /**
     * Magically cast this instance to URL string.
     */
    public function __toString(): string
    {
        return $this->url;
    }

    /**
     * Create a link instance from serialized array.
     */
    public static function fromArray(array $value): static
    {
        return App::make(Manager::class)->resolve($value);
    }

    /**
     * Create a link instance from compiled resolver/variant string.
     */
    public static function fromKey(string $key): static
    {
        return static::fromArray(static::parseKey($key));
    }

    /**
     * Compile a resolver/variant pair string.
     */
    public static function formatKey(string $resolver, null|int|string $variant = null): string
    {
        return (! is_null($variant))
            ? $resolver.'@'.$variant
            : $resolver;
    }

    /**
     * Decompile a resolver/variant pair string.
     */
    public static function parseKey(string $key): array
    {
        $parts = explode('@', $key);

        return [
            'resolver' => $parts[0],
            'variant' => $parts[1] ?? null,
        ];
    }

    /**
     * Compile a data (or "arguments", "parameters") string
     */
    public static function formatDataString(array $data): string
    {
        return implode(',', array_map(function($attribute, $value) {
            return static::quoteDataString($attribute).':'.static::quoteDataString(strval($value));
        }, array_keys($data), array_values($data)));
    }

    /**
     * Compile a data (or "arguments", "parameters") string
     */
    public static function quoteDataString(string $chunk): string
    {
        $triggers = [' ', '"', '\'', ','];

        foreach ($triggers as $trigger) {
            if(strpos($chunk, $trigger) === false) continue;
            return '"'.str_replace(['"','\''], ['\\"','\\\''], $chunk).'"';
        }

        return $chunk;
    }

    /**
     * Decompile a data (or "arguments", "parameters") string
     */
    public static function parseDataSring(string $data): array
    {
        $chunks = [];
        $chunk = '';
        $quoted = false;

        while(strlen($data)) {
            $char = substr($data, 0, 1);
            $data = substr($data, 1);

            if($char === '"') {
                $quoted = !$quoted;
            } elseif ($char === ',' && ! $quoted) {
                $chunks[] = $chunk;
                $chunk = '';
            } else {
                $chunk .= ($char === ':' && $quoted) ? '#__COLUMN__#' : $char;
            }

            if(! strlen($data) && strlen($chunk)) {
                $chunks[] = $chunk;
            }
        }

        return array_reduce($chunks, function($attributes, $chunk) {
            [$key, $value] = array_slice(array_pad(explode(':', $chunk), 2, null), 0, 2);
            $attributes[$key] = str_replace('#__COLUMN__#',':',$value);
            return $attributes;
        }, []);
    }

    /**
     * Create a link instance from an inline tag (string).
     */
    public static function fromInlineTag(string $value): static
    {
        preg_match('/^\\#link\[(.+?)(?:\\,(.+))?\\]$/', trim($value), $matches);
        $parts = array_combine(['full','key','data'], array_pad($matches, 3, null));

        if(is_null($parts['full']) || ! $parts['key']) {
            throw InvalidSerializedValue::inlineTagSyntax($value);
        }

        $unserialized = static::parseKey($parts['key']);

        if($parts['data']) {
            $unserialized['data'] = static::parseDataSring($parts['data']);
        }

        return static::fromArray($unserialized);
    }

    /**
     * Compile a resolver/variant pair string.
     */
    public static function formatInlineTag(string $resolver, null|int|string $variant = null, array $data = []): string
    {
        $tag = '#link[';
        $tag .= static::formatKey($resolver, $variant);

        if($data) {
            $tag .= ','.static::formatDataString($data);
        }

        $tag .= ']';

        return $tag;
    }

    /**
     * Define the link's default displayable title.
     */
    public function title(string $title): static
    {
        $this->title = trim($title) ?: null;

        return $this;
    }

    /**
     * Get the link's displayable title.
     */
    public function getTitle(): string
    {
        return $this->title ?? $this->resolver->getTitle($this->variant);
    }

    /**
     * Transform this link into a parsable pseudo-Blade tag.
     */
    public function toArray(): array
    {
        return array_filter([
            'resolver' => $this->resolver->key,
            'variant' => $this->variant?->getKey(),
            'data' => $this->data ?: null,
        ]);
    }

    /**
     * Transform this link into a parsable resolver/variant key pair.
     */
    public function toKey(): string
    {
        return static::formatKey(
            $this->resolver->key,
            $this->variant?->getKey(),
        );
    }

    /**
     * Transform this link into a parsable inline tag.
     */
    public function toInlineTag(): string
    {
        return static::formatInlineTag(
            $this->resolver->key,
            $this->variant?->getKey(),
            $this->data,
        );
    }
}
