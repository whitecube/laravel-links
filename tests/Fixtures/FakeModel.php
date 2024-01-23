<?php

namespace Whitecube\Links\Tests\Fixtures;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FakeModel extends Model
{
    public static array $items = [
        ['id' => 1, 'slug' => 'one', 'title' => 'Post One'],
        ['id' => 2, 'slug' => 'two', 'title' => 'Post Two'],
        ['id' => 3, 'slug' => 'three', 'title' => 'Post Three'],
    ];

    protected $guarded = [];

    /**
     * Begin querying the model.
     */
    public static function query()
    {
        return new class(static::$items) implements Builder {
            protected ?int $limit = null;
            public function __construct(protected array $items) {}
            public function get(): Collection
            {
                $results = new Collection($this->items);
                if(!is_null($this->limit)) $results = $results->take($this->limit);
                return $results->map(fn($data) => new FakeModel($data));
            }
            public function limit(int $items): static
            {
                $this->limit = $items;
                return $this;
            }
        };
    }
}
