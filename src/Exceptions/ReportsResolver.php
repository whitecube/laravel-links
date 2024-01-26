<?php

namespace Whitecube\Links\Exceptions;

use Whitecube\Links\ResolverInterface;

trait ReportsResolver
{
    /**
     * The attached resolver instance.
     */
    protected ?ResolverInterface $resolver = null;

    /**
     * Attach the eventual concerned resolver instance 
     * in order to be able to report resolving errors properly.
     */
    public function resolver(?ResolverInterface $instance = null): static
    {
        $this->resolver = $instance;

        return $this;
    }

    /**
     * Return the eventual attached resolver instance 
     * in order to be able to report resolving errors properly.
     */
    public function getResolver(): ?ResolverInterface
    {
        return $this->resolver;
    }
}
