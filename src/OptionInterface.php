<?php

namespace Whitecube\Links;

use Closure;

interface OptionInterface
{
    /**
     * Return the identification key for this link option's URL resolver.
     */
    public function getResolverKey(): string;
    
    /**
     * Return the identification key for the eventual object/resource/model represented by this link option.
     */
    public function getVariantKey(): null|int|string;

    /**
     * Define the link option's default displayable title.
     */
    public function title(string $title): static;

    /**
     * Return the link option's default displayable title.
     */
    public function getTitle(): string;

    /**
     * Define eventual extra arguments for the URL resolver. Arguments
     * need to be "stringable" values for serialization.
     */
    public function arguments(array $arguments): static;

    /**
     * Define a single extra argument for the URL resolver. Provided value
     * should be "stringable" values for serialization.
     */
    public function argument(string $attribute, mixed $value): static;

    /**
     * Return all the defined URL resolver arguments.
     */
    public function getArguments(): array;

    /**
     * Check if this option has a displayable panel of sub-options.
     */
    public function hasChoices(): bool;

    /**
     * Define a displayable panel of sub-options.
     */
    public function choices(Closure $setup): static;

    /**
     * Return the option's displayable panel of sub-options when defined.
     */
    public function getChoices(): ?OptionPanel;
}