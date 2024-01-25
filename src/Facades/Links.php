<?php

namespace Whitecube\Links\Facades;

use Illuminate\Support\Facades\Facade;

class Links extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Whitecube\Links\Manager::class;
    }
}
