<?php

namespace Whitecube\Links\Exceptions;

use Whitecube\Links\ResolverInterface;

interface ResolvingException
{
    /**
     * Attach the eventual concerned resolver instance 
     * in order to be able to report resolving errors properly.
     */
    public function resolver(?ResolverInterface $instance = null): static;

    /**
     * Return the eventual attached resolver instance 
     * in order to be able to report resolving errors properly.
     */
    public function getResolver(): ?ResolverInterface;
}
