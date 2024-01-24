<?php

namespace Whitecube\Links;

class OptionPanel
{
    /**
     * The options to display at the top of the panel.
     */
    protected array $before = [];

    /**
     * The options to display at the bottom of the panel.
     */
    protected array $after = [];

    /**
     * The dynamically loaded, paginated & searchable options.
     */
    protected ?OptionsCollection $archive = null;

    /**
     * Add an option to the top of the panel.
     */
    public function prepend(OptionInterface $option): static
    {
        $this->before[] = $option;

        return $this;
    }

    /**
     * Add an option to the end of the panel.
     */
    public function append(OptionInterface $option): static
    {
        $this->after[] = $option;

        return $this;
    }

    /**
     * Define the panel's dynamic, paginated & searchable options archive.
     */
    public function archive(OptionsCollection $archive): static
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Return the panel's defined options/features in the correct order.
     */
    public function options(): array
    {
        return array_values(array_filter(array_merge(
            $this->before,
            [$this->archive],
            $this->after,
        )));
    }
}
