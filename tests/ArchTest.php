<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Facade;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types are used')
    ->expect('Fridzema\ValidationPlus')
    ->toUseStrictTypes();

arch('facades extend the base Facade class')
    ->expect('Fridzema\ValidationPlus\Facades')
    ->toExtend(Facade::class);

arch('middleware implements handle method')
    ->expect('Fridzema\ValidationPlus\Middleware')
    ->toHaveMethod('handle');
