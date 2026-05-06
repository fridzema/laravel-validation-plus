<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Support\Facades\Blade;

it('renders nothing when warnings bag is empty', function (): void {
    $html = Blade::render('<x-validation-plus::warnings />');

    expect(trim($html))->toBe('');
});

it('renders warning messages when bag has messages', function (): void {
    app(WarningBag::class)->merge(['name' => ['Name is too short.']]);

    $html = Blade::render('<x-validation-plus::warnings />');

    expect($html)->toContain('Name is too short.');
});

it('includes default alert-warning class', function (): void {
    app(WarningBag::class)->merge(['name' => ['Warning.']]);

    $html = Blade::render('<x-validation-plus::warnings />');

    expect($html)->toContain('alert-warning');
});

it('merges additional classes via attributes', function (): void {
    app(WarningBag::class)->merge(['name' => ['Warning.']]);

    $html = Blade::render('<x-validation-plus::warnings class="my-custom-class" />');

    expect($html)->toContain('my-custom-class');
    expect($html)->toContain('alert-warning');
});
