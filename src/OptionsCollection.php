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

    /**
     * Return the total amount of options that should be contained in this collection.
     * TODO : this method anticipates the development of paginated OptionsCollections used
     * in huge Archive resolvers.
     */
    public function total(): int
    {
        return $this->count();
    }
}