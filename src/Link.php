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
     * Create a link instance from an inline tag (string).
     */
    public static function fromInlineTag(string $value): static
    {
        preg_match('/^\\@link\\((.+?)\\)$/', trim($value), $matches);

        if(! ($matches[1] ?? null)) {
            throw InvalidSerializedValue::inlineTagSyntax($value);
        }

        $data = array_reduce(explode(',', $matches[1]), function ($data, $pair) {
            $pair = explode(':', $pair);
            if(! isset($pair[0]) || ! isset($pair[1])) return $data;
            $data[$pair[0]] = $pair[1];
            return $data;
        }, []);

        return static::fromArray($data);
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
     * Transform this link into a parsable pseudo-Blade tag.
     */
    public function toInlineTag(): string
    {
        $arguments = $this->toArray();

        $tag = '@link(';
        $tag .= implode(',', array_map(fn($key, $value) => $key.':'.$value, array_keys($arguments), array_values($arguments)));
        $tag .= ')';

        return $tag;
    }
}
