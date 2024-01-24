<?php

namespace Whitecube\Links;

class Option implements OptionInterface
{
    /**
     * The URL resolver's identification key.
     */
    protected string $resolver;

    /**
     * The represented variant object's identification key.
     */
    protected null|int|string $variant = null;

    /**
     * The default displayable title.
     */
    protected ?string $title;

    /**
     * The extra resolver arguments.
     */
    protected array $arguments = [];

    /**
     * The sub-options for this parent option.
     */
    protected array $children = [];

    /**
     * Create a new Link Option instance.
     */
    public function __construct(string $resolver, null|int|string $variant = null)
    {
        $this->resolver = $resolver;
        $this->variant = $variant;
    }

    /**
     * Return the identification key for this link option's URL resolver.
     */
    public function getResolverKey(): string
    {
        return $this->resolver;
    }
    
    /**
     * Return the identification key for the eventual object/resource/model represented by this link option.
     */
    public function getVariantKey(): null|int|string
    {
        return $this->variant;
    }
    
    /**
     * Return the full resolver+variant identification key.
     */
    public function getKey(): string
    {
        $key = $this->getResolverKey();

        if($variant = $this->getVariantKey()) {
            $key .= '@'.$variant;
        }

        return $key;
    }

    /**
     * Define the link option's default displayable title.
     */
    public function title(string $title): static
    {
        $this->title = strlen($title) ? $title : null;

        return $this;
    }

    /**
     * Return the link option's default displayable title.
     */
    public function getTitle(): string
    {
        return $this->title ?? $this->getKey();
    }

    /**
     * Define eventual extra arguments for the URL resolver. Arguments
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
     * Define a single extra argument for the URL resolver. Provided value
     * should be "stringable" values for serialization.
     */
    public function argument(string $attribute, mixed $value): static
    {
        $this->arguments[$attribute] = is_null($value) ? null : strval($value);

        return $this;
    }

    /**
     * Return all the defined URL resolver arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Magically access an argument value.
     */
    public function __get(string $argument): mixed
    {
        return $this->arguments[$argument] ?? null;
    }

    /**
     * Magically set an argument value.
     */
    public function __set(string $argument, mixed $value): void
    {
        $this->argument($argument, $value);
    }

    /**
     * Set an array of sub-options.
     */
    public function children(array $options): static
    {
        $this->children = array_filter($options, fn($option) => is_a($option, OptionInterface::class));

        return $this;
    }

    /**
     * Check if this option has sub-options.
     */
    public function hasChildren(): bool
    {
        return ! empty($this->children);
    }

    /**
     * Return the eventual sub-options.
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}