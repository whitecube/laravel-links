<?php

namespace Whitecube\Links\Resolvers;

use Closure;
use Whitecube\Links\OptionPanel;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\ResolverInterface;

class Archive implements ResolverInterface
{
    use Concerns\HasOption;
    
    const INDEX_KEY = 'index';
    const ITEM_KEY = 'item';

    /**
     * The resolver's identification key.
     */
    public readonly string $key;

    /**
     * The archive's index URL resolver.
     */
    protected ?ArchiveIndexRoute $index = null;

    /**
     * The archive's index URL resolver.
     */
    protected ?ArchiveItemsRoute $items = null;

    /**
     * Create a new simple Route Resolver.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Define the archive's index URL resolver.
     */
    public function index(Closure $setup): static
    {
        $this->index = new ArchiveIndexRoute($this->key.'.'.static::INDEX_KEY);

        $setup($this->index);

        return $this;
    }

    /**
     * Define the archive's items URLs resolver.
     */
    public function items(Closure $setup): static
    {
        $this->items = new ArchiveItemsRoute($this->key.'.'.static::ITEM_KEY);

        $setup($this->items);

        return $this;
    }

    /**
     * Check if this resolver is suited for the provided key and return
     * itself or a more appropriate/specific resolver if available.
     */
    public function for(string $key): ?ResolverInterface
    {
        return ($this->key === $key ? $this : null) 
            ?? $this->index?->for($key)
            ?? $this->items?->for($key)
            ?? null;
    }

    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection
    {
        if(! $this->items && ! $this->index) {
            return null;
        }

        if(! $this->items) {
            return $this->index->toOption();
        }

        return $this->getOptionInstance()
            ->title($this->getTitle())
            ->choices(fn(OptionPanel $panel) => $this->configureOptionChoicesPanel($panel));
    }

    /**
     * Handle the archives's displayable sub-options panel.
     */
    protected function configureOptionChoicesPanel(OptionPanel $panel): void
    {
        if($this->index) {
            $panel->prepend($this->index->toOption());
        }

        $panel->archive($this->items->toOption());
    }

    /**
     * Generate the effective URL.
     */
    public function resolve(array $arguments = []): string
    {
        return '#'; // TODO
    }
}
