<?php

use Illuminate\Support\Facades\App;
use Whitecube\Links\Option;
use Whitecube\Links\OptionInterface;

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

// uses(Tests\TestCase::class)->in('Feature');

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

expect()->extend('toBeWorkingArchiveResolver', function () {
    // TODO : add common archive resolver tests.
    // dd($this);
});

expect()->extend('toBeWorkingArchiveIndexResolver', function () {
    // TODO : add common archive resolver tests.
    // dd($this);
});

expect()->extend('toBeWorkingArchiveItemsResolver', function () {
    // TODO : add common archive resolver tests.
    // dd($this);
});

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

function setupAppBindings()
{
    App::swap(new class() {
        public function makeWith(string $classname, array $arguments = []) {
            return match ($classname) {
                OptionInterface::class => new Option(...$arguments),
                default => null,
            };
        }
    });
}
