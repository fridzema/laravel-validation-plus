<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class ValidationPlusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('validation-plus');
    }
}
