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

it('@warning directive renders message for matching key', function (): void {
    app(WarningBag::class)->merge(['name' => ['Name is too short.']]);

    $html = Blade::render('@warning(\'name\')<p>{{ $message }}</p>@endwarning');

    expect($html)->toContain('Name is too short.');
});

it('@warning directive renders nothing when key has no warnings', function (): void {
    $html = Blade::render('@warning(\'name\')<p>{{ $message }}</p>@endwarning');

    expect(trim($html))->toBe('');
});

it('@warning directive iterates multiple messages', function (): void {
    app(WarningBag::class)->merge(['name' => ['First warning.', 'Second warning.']]);

    $html = Blade::render('@warning(\'name\')<p>{{ $message }}</p>@endwarning');

    expect($html)->toContain('First warning.');
    expect($html)->toContain('Second warning.');
});

it('@warning directive works with global warnings', function (): void {
    app(WarningBag::class)->addGlobal('Storage limit approaching.');

    $html = Blade::render('@warning(\'__global__\')<p>{{ $message }}</p>@endwarning');

    expect($html)->toContain('Storage limit approaching.');
});
