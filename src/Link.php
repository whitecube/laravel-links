<?php

namespace Whitecube\Links;

use Whitecube\Links\Exceptions\InvalidSerializedValue;

class Link
{
    /**
     * The URL resolver's identification key.
     */
    protected string $key;

    /**
     * The represented resource's immutable identifier.
     */
    protected null|int|string $id;

    /**
     * The default displayable title.
     */
    protected string $title;

    /**
     * The eventual extra arguments for the URL resolver.
     */
    protected array $arguments = [];

    /**
     * Create a new Link instance.
     */
    public function __construct(string $key, null|int|string $id = null)
    {
        $this->key = $key;
        $this->id = $id;
    }

    /**
     * Create a new Link instance dynamically.
     */
    public static function make(string $key, null|int|string $id = null): static
    {
        return new static($key, $id);
    }

    /**
     * Create a link instance from serialized array.
     */
    public static function fromArray(array $value): static
    {
        if(! isset($value['key'])) {
            return InvalidSerializedValue::forArray();
        }

        $instance = static::make($value['key'], $value['id'] ?? null);

        unset($value['key']);
        unset($value['id']);

        return $instance->arguments($value);
    }

    /**
     * Create a link instance from an inline tag (string).
     */
    public static function fromInlineTag(string $value): static
    {
        preg_match('/^\\@link\\((.+?)\\)$/', trim($value), $matches);

        if(! ($matches[1] ?? null)) {
            return InvalidSerializedValue::forInlineTag($value);
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
     * Get the link's resolver identifying key
     */
    public function getResolverKey(): string
    {
        return $this->key;
    }

    /**
     * Define the link's default displayable title.
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the link's displayable title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Define eventual extra arguments for the link's URL resolver. Arguments
     * need to be "stringable" values for serialization.
     */
    public function arguments(array $arguments): static
    {
        foreach($arguments as $attribute => $value) {
            $this->argument($attribute, $value);
        }

        return $this;
    }

    /**
     * Define a single extra argument for the link's URL resolver. Provided value
     * should be "stringable" values for serialization.
     */
    public function argument(string $attribute, mixed $value): static
    {
        $this->arguments[$attribute] = is_null($value) ? null : strval($value);

        return $this;
    }

    /**
     * Transform this link into a parsable pseudo-Blade tag.
     */
    public function toArray(): array
    {
        return array_filter(array_merge([
            'key' => $this->key,
            'id' => $this->id,
        ], $this->arguments));
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
