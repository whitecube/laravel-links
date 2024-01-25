<?php

namespace Whitecube\Links\Resolvers;

use Whitecube\Links\Link;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Exceptions\VariantNotFound;
use Whitecube\Links\Exceptions\InvalidSerializedValue;

class ArchiveItemsRoute implements ResolverInterface
{
    use Concerns\HasOption;
    use Concerns\HasVariants;
    use Concerns\ResolvesRoutes;

    /**
     * The resolver's identification key.
     */
    public readonly string $key;

    /**
     * Create a new archive collection Route Resolver.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Check if this resolver is suited for the provided key and return
     * itself or a more appropriate/specific resolver if available.
     */
    public function for(string $key): ?ResolverInterface
    {
        return ($this->key === $key) ? $this : null;
    }

    /**
     * Instantiate a Link object based on provided serialized value.
     */
    public function resolve(array $value, bool $silent): ?Link
    {
        if(! isset($value['variant'])) {
            if($silent) return null;
            throw InvalidSerializedValue::missingVariant();
        }

        $variant = $this->findVariant($value['variant']);

        if(! $variant) {
            if($silent) return null;
            throw VariantNotFound::forKey($value['variant']);
        }
        
        return new Link(
            url: $this->generateUrl(variant: $variant, parameters: $value['data'] ?? []),
            data: $value['data'] ?? [],
            resolver: $this,
            variant: $variant,
        );
    }

    /**
     * Transform the resolver into an available Link Option.
     */
    public function toOption(): null|OptionInterface|OptionsCollection
    {
        return $this->toOptionsCollection(
            $this->getAllVariants()
        );
    }
}
