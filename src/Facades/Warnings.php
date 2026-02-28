<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus\Facades;

use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Fridzema\ValidationPlus\WarningBag
 */
final class Warnings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WarningBag::class;
    }
}
