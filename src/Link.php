<?php

namespace Whitecube\Links;

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
     * Define the link's default displayable title.
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
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
        $this->arguments[$attribute] = strval($value);

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
