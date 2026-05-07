<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Fridzema\ValidationPlus\WarningBag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory;

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

it('skips warning evaluation when warningRules is empty', function (): void {
    $request = createFormRequest(
        data: ['email' => 'test@test.com'],
        rules: ['email' => 'required|email'],
    );

    $request->validateResolved();

    expect(app(WarningBag::class)->isEmpty())->toBeTrue();
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

it('has default empty warning attributes', function (): void {
    $request = new class extends FormRequest
    {
        use HasWarningRules;

        public function rules(): array
        {
            return [];
        }
    };

    expect($request->warningAttributes())->toBe([]);
});

it('supports custom warning attribute names via warningAttributes()', function (): void {
    $request = createFormRequest(
        data: ['user_name' => 'AB'],
        rules: ['user_name' => 'required|string'],
        warningRules: ['user_name' => 'min:3'],
        warningAttributes: ['user_name' => 'display name'],
    );

    $request->validateResolved();

    $warningBag = app(WarningBag::class);
    expect($warningBag->first('user_name'))->toContain('display name');
});

it('resolves warningRules via container to support dependency injection', function (): void {
    $request = new class extends FormRequest
    {
        use HasWarningRules;

        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return ['email' => 'required|email'];
        }

        public function warningRules(Factory $factory): array
        {
            // $factory injected by container — direct call without container would throw ArgumentCountError
            return ['email' => 'in:unique@test.com'];
        }
    };

    $request = $request::create('/', 'POST', ['email' => 'other@test.com']);
    $request->setContainer(app());
    $request->validateResolved();

    expect(app(WarningBag::class)->has('email'))->toBeTrue();
});

it('supports dot-notation nested field warning rules', function (): void {
    $request = createFormRequest(
        data: ['profile' => ['name' => 'Jo']],
        rules: ['profile.name' => 'required|string'],
        warningRules: ['profile.name' => 'min:3'],
    );

    $request->validateResolved();

    $warningBag = app(WarningBag::class);
    expect($warningBag->has('profile.name'))->toBeTrue();
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
