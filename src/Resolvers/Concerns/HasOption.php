<?php

namespace Whitecube\Links\Resolvers\Concerns;

use Closure;
use Whitecube\Links\Variant;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsArchive;
use Whitecube\Links\OptionsCollection;
use Illuminate\Support\Facades\App;

trait HasOption
{
    /**
     * The resolver's displayable option title.
     */
    protected null|string|Closure $title = null;

    /**
     * Set resolver's displayable option title.
     */
    public function title(string|Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Resolve the displayable option title.
     */
    public function getTitle(Variant $variant = null): string
    {
        $argument = (! is_null($variant))
            ? ($variant->isStructure() ? $variant : $variant->raw())
            : null;

        $value = is_a($this->title, Closure::class)
            ? strval(call_user_func($this->title, $argument))
            : ($this->title ?? '');

        return trim($value);
    }

    /**
     * Transform provided variants into displayable option instances.
     */
    protected function toOptionsCollection(array $variants): OptionsCollection
    {
        $options = array_map(function(Variant $variant) {
            return $this->getOptionInstance($variant->getKey())
                ->title($this->getTitle($variant));
        }, $variants);

        return $this->getOptionsCollection($options);
    }

    /**
     * Create a new empty option instance for this resolver.
     */
    protected function getOptionInstance(null|int|string $variant = null): OptionInterface
    {
        return App::makeWith(OptionInterface::class, ['resolver' => $this->key, 'variant' => $variant]);
    }

    /**
     * Create a new empty collection of options for this resolver
     */
    protected function getOptionsCollection(array $options): OptionsCollection
    {
        return App::makeWith(OptionsArchive::class, ['items' => $options]);
    }
}