<?php

namespace Whitecube\Links;

use Illuminate\Support\Collection;

class OptionsCollection extends Collection
{
    /**
     * Results array of items from Collection or Arrayable.
     */
    protected function getArrayableItems($items)
    {
        return array_values(array_filter(
            parent::getArrayableItems($items),
            fn($item) => is_a($item, OptionInterface::class)
        ));
    }
}