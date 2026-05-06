<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Support\MessageBag;

it('can be instantiated empty', function (): void {
    $bag = new WarningBag;

    expect($bag->isEmpty())->toBeTrue();
    expect($bag->any())->toBeFalse();
});

it('can be instantiated with messages', function (): void {
    $bag = new WarningBag(['email' => ['This email is already taken.']]);

    expect($bag->isEmpty())->toBeFalse();
    expect($bag->any())->toBeTrue();
    expect($bag->first('email'))->toBe('This email is already taken.');
});

it('can get all messages', function (): void {
    $bag = new WarningBag([
        'email' => ['Email warning.'],
        'name' => ['Name warning.'],
    ]);

    expect($bag->all())->toHaveCount(2);
});

it('can check if a key has warnings', function (): void {
    $bag = new WarningBag(['email' => ['Warning.']]);

    expect($bag->has('email'))->toBeTrue();
    expect($bag->has('name'))->toBeFalse();
});

it('can get messages for a key', function (): void {
    $bag = new WarningBag(['email' => ['Warning one.', 'Warning two.']]);

    expect($bag->get('email'))->toHaveCount(2);
});

it('is an instance of MessageBag', function (): void {
    $bag = new WarningBag;

    expect($bag)->toBeInstanceOf(MessageBag::class);
});

it('can add and detect global warnings', function (): void {
    $bag = new WarningBag;

    expect($bag->hasGlobal())->toBeFalse();

    $bag->addGlobal('Your account is almost full.');

    expect($bag->hasGlobal())->toBeTrue();
    expect($bag->get('__global__'))->toContain('Your account is almost full.');
});

it('addGlobal is chainable', function (): void {
    $bag = new WarningBag;

    $bag->addGlobal('First.')->addGlobal('Second.');

    expect($bag->get('__global__'))->toHaveCount(2);
});
