<?php

namespace Whitecube\Links;

class OptionsCollection
{
    /**
     * The defined options.
     */
    protected array $options = [];

    /**
     * Create an options collections.
     */
    public function __construct(array $options)
    {
        $this->options = array_values(array_filter($options, fn($value) => is_a($value, OptionInterface::class)));
    }

    /**
     * Get the first defined option.
     */
    public function first(): ?OptionInterface
    {
        return $this->options[0] ?? null;
    }

    /**
     * Get the amount of defined options.
     */
    public function total(): int
    {
        return count($this->options);
    }

    /**
     * Get all defined options as array.
     */
    public function all(): array
    {
        return $this->options;
    }
}