<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Foundation\Http\FormRequest;

it('has default empty warning rules', function (): void {
    $request = new class extends FormRequest
    {
        use HasWarningRules;

        public function rules(): array
        {
            return [];
        }
    };

    expect($request->warningRules())->toBe([]);
});

it('has default empty warning messages', function (): void {
    $request = new class extends FormRequest
    {
        use HasWarningRules;

        public function rules(): array
        {
            return [];
        }
    };

    expect($request->warningMessages())->toBe([]);
});

it('populates warning bag after validation passes', function (): void {
    $request = createFormRequest(
        data: ['email' => 'not-unique@test.com'],
        rules: ['email' => 'required|email'],
        warningRules: ['email' => 'in:unique@test.com'],
    );

    $request->validateResolved();

    $warningBag = app(WarningBag::class);

    expect($warningBag->has('email'))->toBeTrue();
});

it('does not populate warnings when warning rules pass', function (): void {
    $request = createFormRequest(
        data: ['email' => 'unique@test.com'],
        rules: ['email' => 'required|email'],
        warningRules: ['email' => 'in:unique@test.com'],
    );

    $request->validateResolved();

    $warningBag = app(WarningBag::class);

    expect($warningBag->isEmpty())->toBeTrue();
});

it('supports custom warning messages', function (): void {
    $request = createFormRequest(
        data: ['email' => 'not-unique@test.com'],
        rules: ['email' => 'required|email'],
        warningRules: ['email' => 'in:unique@test.com'],
        warningMessages: ['email.in' => 'This email may already be in use.'],
    );

    $request->validateResolved();

    $warningBag = app(WarningBag::class);

    expect($warningBag->first('email'))->toBe('This email may already be in use.');
});
