<?php

namespace Whitecube\Links;

class OptionsArchive extends OptionsCollection
{
    /**
     * Return the total amount of options that should be contained in this collection.
     * TODO : this method anticipates the development of paginated OptionsCollections used
     * in huge Archive resolvers.
     */
    public function total(): int
    {
        return $this->count();
    }

    /**
     * Prepare the archive for proper front-end display.
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => 'archive',
            'total' => $this->total(),
            // TODO :
            // 'page' => 1,
            // 'urls' => [
            //     'load' => '#next-page-url',
            //     'search' => '#search-url',
            // ],
            'items' => parent::jsonSerialize(),
        ];
    }
}