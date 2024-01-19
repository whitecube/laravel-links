<?php

namespace Whitecube\Links\Resolvers\Concerns;

use Closure;
use Illuminate\Support\Facades\App;
use Whitecube\Links\OptionInterface;

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
    public function getTitle(mixed $item = null): string
    {
        if(is_null($this->title)) {
            return $this->key;
        }

        if(is_a($this->title, Closure::class)) {
            return call_user_func_array($this->title, $item);
        }

        return $this->title;
    }

    /**
     * Create a new empty option instance for this resolver.
     */
    protected function getOptionInstance(): OptionInterface
    {
        return App::makeWith(OptionInterface::class, ['resolver' => $this->key]);
    }
}