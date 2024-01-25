<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends OrchestraTestCase
{
    use WithWorkbench;
}
