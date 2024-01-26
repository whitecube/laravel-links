<?php

namespace Whitecube\Links\Tests;

use Orchestra\Testbench\TestCase as BaseOrchestraTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use function Orchestra\Testbench\artisan;

class OrchestraTestCase extends BaseOrchestraTestCase
{
    use WithWorkbench;

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function defineDatabaseMigrations()
    {
        artisan($this, 'migrate', ['--database' => 'testing', '--seed' => true, '--seeder' => 'Workbench\\Database\\Seeders\\DatabaseSeeder']);

        $this->beforeApplicationDestroyed(
            fn () => artisan($this, 'migrate:rollback', ['--database' => 'testing'])
        );
    }
}
