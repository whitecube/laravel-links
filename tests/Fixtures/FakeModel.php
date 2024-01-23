<?php

namespace Whitecube\Links\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FakeModel extends Model
{
    /**
     * Begin querying the model.
     */
    public static function query()
    {
        return new class() implements Builder {};
    }
}
