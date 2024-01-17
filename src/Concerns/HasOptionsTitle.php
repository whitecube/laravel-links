<?php

namespace Whitecube\Links\Concerns;

use Closure;

trait HasOptionsTitle
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
    public function getTitle(...$arguments): string
    {
        if(is_null($this->title)) {
            return $this->key;
        }

        if(is_a($this->title, Closure::class)) {
            return call_user_func_array($this->title, $arguments);
        }

        return $this->title;
    }
}