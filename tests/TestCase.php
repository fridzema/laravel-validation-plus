<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus\Tests;

use Fridzema\ValidationPlus\ValidationPlusServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ValidationPlusServiceProvider::class,
        ];
    }
}
