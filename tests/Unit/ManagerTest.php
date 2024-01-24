<?php

use Whitecube\Links\Manager;
use Whitecube\Links\OptionInterface;
use Whitecube\Links\OptionsCollection;
use Whitecube\Links\OptionsCollectionBuilder;
use Whitecube\Links\ResolverInterface;
use Whitecube\Links\Exceptions\ResolverNotFound;
use Whitecube\Links\Tests\Fixtures\FakeModel;
use Whitecube\Links\Tests\Fixtures\FakeResolver;

it('can define and find a resolver instance', function () {
    $service = new Manager();
    $resolver = new FakeResolver('foo');

    $service->register($resolver);

    expect($service->for('foo'))->toBe($resolver);
    expect($service->tryFor('foo'))->toBe($resolver);
});

it('cannot find an undefined resolver instance', function () {
    $service = new Manager();

    $service->for('foo');
})->throws(ResolverNotFound::class, 'Link resolver for key "foo" is undefined.');

it('cannot find an undefined resolver instance and return null', function () {
    $service = new Manager();

    expect($service->tryFor('foo'))->toBeNull();
});

it('can register macros', function () {
    Manager::macro('foo', function(string $name) {
        $resolver = new class ($name) implements ResolverInterface {
            public function __construct(public string $key) {}
            public function for(string $key): ?ResolverInterface { return ($key === $this->key) ? $this : null; }
            public function toOption(): null|OptionInterface|OptionsCollection { return null; }
            public function resolve(array $arguments = []): string { return '#'; }
        };
        $this->register($resolver);
        return $resolver;
    });

    $service = new Manager();
    $service->foo('bar');

    expect($service->tryFor('bar'))->toBeInstanceOf(ResolverInterface::class);
    expect($service->tryFor('foo'))->toBeNull();
    expect($service->for('bar'))->toBeInstanceOf(ResolverInterface::class);
});

it('can return link options list', function () {
    setupAppBindings();

    $service = new Manager();
    $service->route('home')->title('Homepage');
    $service->route('foo')->title(fn() => date('d-m-Y'));
    $service->route('bar');

    expect($service->options())->toBeInstanceOf(OptionsCollectionBuilder::class);
    expect($service->options()->get())->toBeInstanceOf(OptionsCollection::class);
    expect($service->options()->toCollection())->toBeInstanceOf(OptionsCollection::class);
    expect($service->options()->all())->toBeArray();
    expect($service->options()->toArray())->toBeArray();

    $options = $service->options()->toArray();

    expect($options)->toHaveCount(3);
    expect($options[0])->toBeInstanceOf(OptionInterface::class);
    expect($options[0]->getResolverKey())->toBe('home');
    expect($options[0]->getTitle())->toBe('Homepage');
    expect($options[1])->toBeInstanceOf(OptionInterface::class);
    expect($options[1]->getResolverKey())->toBe('foo');
    expect($options[1]->getTitle())->toBe(date('d-m-Y'));
    expect($options[2])->toBeInstanceOf(OptionInterface::class);
    expect($options[2]->getResolverKey())->toBe('bar');
    expect($options[2]->getTitle())->toBe('bar');
});

it('can return link options list without unwanted resolvers', function () {
    setupAppBindings();

    $service = new Manager();
    $service->route('home')->title('Homepage');
    $service->route('foo')->title(fn() => date('d-m-Y'));
    $service->route('bar');

    $withoutHome = $service->options()->except('home')->toArray();
    expect($withoutHome)->toHaveCount(2);
    expect($withoutHome[0]->getResolverKey())->toBe('foo');
    expect($withoutHome[1]->getResolverKey())->toBe('bar');

    $withoutFooBar = $service->options()->except(['foo','bar'])->toArray();
    expect($withoutFooBar)->toHaveCount(1);
    expect($withoutFooBar[0]->getResolverKey())->toBe('home');
});

it('can return link options list of selected resolvers', function () {
    setupAppBindings();

    $service = new Manager();
    $service->route('home')->title('Homepage');
    $service->route('foo')->title(fn() => date('d-m-Y'));
    $service->route('bar');

    $onlyHome = $service->options()->only('home')->toArray();
    expect($onlyHome)->toHaveCount(1);
    expect($onlyHome[0]->getResolverKey())->toBe('home');

    $onlyFooBar = $service->options()->only(['foo','bar'])->toArray();
    expect($onlyFooBar)->toHaveCount(2);
    expect($onlyFooBar[0]->getResolverKey())->toBe('foo');
    expect($onlyFooBar[1]->getResolverKey())->toBe('bar');
});

it('can serialize link options list as JSON', function () {
    setupAppBindings();

    $service = new Manager();
    $service->route('home')->title('Homepage');
    $service->route('foo')->title(fn() => date('d-m-Y'));
    $service->route('bar');
    $service->archive('posts')
        ->title('Latest news')
        ->index(fn ($entry) => $entry->route('posts')->title('All latest news'))
        ->items(fn ($entry) => $entry->route('post')
            // ->argument('slug', fn ($item) => $item->slug)
            ->title(fn ($item) => $item->title)
            ->collect(FakeModel::$items)
            ->keyBy('id')
        );

    $data = json_decode(json_encode($service->options()->get()), true);

    expect($data)->toMatchArray([
        [
            'type' => 'option',
            'label' => 'Homepage',
            'value' => 'home',
        ],
        [
            'type' => 'option',
            'label' => date('d-m-Y'),
            'value' => 'foo',
        ],
        [
            'type' => 'option',
            'label' => 'bar',
            'value' => 'bar',
        ],
        [
            'type' => 'panel',
            'label' => 'Latest news',
            'options' => [
                [
                    'type' => 'option',
                    'label' => 'All latest news',
                    'value' => 'posts.index',
                ],
                [
                    'type' => 'archive',
                    'total' => 3,
                    'items' => [
                        [
                            'type' => 'option',
                            'label' => 'Post One',
                            'value' => 'posts.item@1',
                        ],
                        [
                            'type' => 'option',
                            'label' => 'Post Two',
                            'value' => 'posts.item@2',
                        ],
                        [
                            'type' => 'option',
                            'label' => 'Post Three',
                            'value' => 'posts.item@3',
                        ],
                    ],
                ],
            ],
        ],
    ]);
});
