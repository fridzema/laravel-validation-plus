<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\WarningBag;
use Fridzema\ValidationPlus\WarningValidator;

it('returns empty warning bag when no rules fail', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['email' => 'test@example.com'],
        ['email' => 'email'],
    );

    expect($result)->toBeInstanceOf(WarningBag::class);
    expect($result->isEmpty())->toBeTrue();
});

it('returns warning bag with messages when rules fail', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['email' => 'not-an-email'],
        ['email' => 'email'],
    );

    expect($result)->toBeInstanceOf(WarningBag::class);
    expect($result->isEmpty())->toBeFalse();
    expect($result->has('email'))->toBeTrue();
});

it('supports custom warning messages', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['email' => 'not-an-email'],
        ['email' => 'email'],
        ['email.email' => 'Custom warning: invalid email format.'],
    );

    expect($result->first('email'))->toBe('Custom warning: invalid email format.');
});

it('supports custom attribute names', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['email_address' => 'not-an-email'],
        ['email_address' => 'email'],
        [],
        ['email_address' => 'email address'],
    );

    expect($result->first('email_address'))->toContain('email address');
});

it('supports multiple rules per field', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['name' => ''],
        ['name' => 'required|min:3'],
    );

    expect($result->get('name'))->not->toBeEmpty();
});

it('supports multiple fields', function (): void {
    $validator = new WarningValidator;

    $result = $validator->validate(
        ['email' => 'bad', 'name' => ''],
        ['email' => 'email', 'name' => 'required'],
    );

    expect($result->has('email'))->toBeTrue();
    expect($result->has('name'))->toBeTrue();
});

it('does not throw validation exception', function (): void {
    $validator = new WarningValidator;

    // This should NOT throw — warnings never throw
    $result = $validator->validate(
        ['email' => 'bad'],
        ['email' => 'email'],
    );

    expect($result)->toBeInstanceOf(WarningBag::class);
});
