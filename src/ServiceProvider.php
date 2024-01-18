<?php

namespace Whitecube\Links;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->app->bind(OptionInterface::class, Option::class);
    }
}
