<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Facades\Warnings;
use Fridzema\ValidationPlus\WarningBag;

it('Warnings facade resolves to WarningBag', function (): void {
    expect(Warnings::getFacadeRoot())->toBeInstanceOf(WarningBag::class);
});

it('can add and retrieve warnings via facade', function (): void {
    Warnings::merge(['field' => ['A warning.']]);

    expect(Warnings::has('field'))->toBeTrue();
    expect(Warnings::first('field'))->toBe('A warning.');
});
