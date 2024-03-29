<?php

use Whitecube\Links\Manager;
use Whitecube\Links\Option;
use Whitecube\Links\OptionsArchive;
use Whitecube\Links\OptionInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(\Whitecube\Links\Tests\OrchestraTestCase::class)->in('Feature');
uses(\Whitecube\Links\Tests\DefaultTestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/


/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function setupAppBindings(?Manager $service = null): Manager
{
    $singleton = $service ?? new Manager;

    App::swap(new class($singleton) {
        public function __construct(protected ?Manager $singleton) {}

        public function make($classname) {
            return match ($classname) {
                Manager::class => $this->singleton,
                default => null,
            };
        }

        public function makeWith(string $classname, array $arguments = []) {
            return match ($classname) {
                OptionInterface::class => new Option(...$arguments),
                OptionsArchive::class => new OptionsArchive(...$arguments),
                default => null,
            };
        }
    });

    return $singleton;
}

function setupRoute(string $name, array $arguments = [], int $times = 1): void
{   
    URL::shouldReceive('route')
        ->times($times)
        ->with($name, $arguments)
        ->andReturn('https://foo.bar/testing-route');
}
