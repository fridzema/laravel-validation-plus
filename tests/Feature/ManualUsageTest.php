<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Middleware\ShareWarnings;
use Fridzema\ValidationPlus\WarningBag;
use Fridzema\ValidationPlus\WarningValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::middleware([ShareWarnings::class])
        ->post('/test-manual', function (Request $request) {
            $warnings = app(WarningValidator::class)->validate(
                $request->all(),
                ['name' => 'min:3'],
                ['name.min' => 'Name is too short.'],
            );
            app(WarningBag::class)->merge($warnings->getMessages());

            return response()->json(['status' => 'ok']);
        });
});

it('supports manual validation in controller context', function (): void {
    $response = $this->postJson('/test-manual', ['name' => 'Jo']);

    $response->assertOk();
    $response->assertHasWarning('name', 'Name is too short.');
});

it('does not generate warnings when manual validation passes', function (): void {
    $response = $this->postJson('/test-manual', ['name' => 'Jonathan']);

    $response->assertOk();
    $response->assertHasNoWarnings();
});

it('WarningValidator is a singleton across resolves', function (): void {
    $a = app(WarningValidator::class);
    $b = app(WarningValidator::class);

    expect($a)->toBe($b);
});
