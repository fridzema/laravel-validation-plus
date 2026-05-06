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

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('x', 32)));
        $app['config']->set('session.driver', 'array');
    }
}
