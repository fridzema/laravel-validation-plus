<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Middleware\ShareWarnings;
use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::middleware([ShareWarnings::class])
        ->post('/test-macros', function (MacroTestFormRequest $request) {
            return response()->json(['status' => 'ok']);
        });
});

it('can assert has warning on a response', function (): void {
    $response = $this->postJson('/test-macros', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertHasWarning('name');
});

it('can assert has warning with specific message', function (): void {
    $response = $this->postJson('/test-macros', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertHasWarning('name', 'Name too short for display.');
});

it('can assert has no warnings', function (): void {
    $response = $this->postJson('/test-macros', [
        'email' => 'test@test.com',
        'name' => 'Jonathan',
    ]);

    $response->assertOk();
    $response->assertHasNoWarnings();
});

it('can assert has no warnings for specific key', function (): void {
    $response = $this->postJson('/test-macros', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertHasNoWarnings('email');
});

it('reads warnings from header when inject_json is false', function (): void {
    config()->set('validation-plus.inject_json', false);

    Route::middleware([ShareWarnings::class])
        ->post('/test-macros-no-inject', function (MacroTestFormRequest $request) {
            return response()->json(['status' => 'ok']);
        });

    $response = $this->postJson('/test-macros-no-inject', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertHasWarning('name', 'Name too short for display.');
});

it('reads warnings from header on 204 responses', function (): void {
    Route::middleware([ShareWarnings::class])
        ->post('/test-macros-204', function (MacroTestFormRequest $request) {
            return response()->noContent();
        });

    $response = $this->postJson('/test-macros-204', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertNoContent();
    $response->assertHasWarning('name', 'Name too short for display.');
});

it('reads warnings from header on scalar json body', function (): void {
    Route::middleware([ShareWarnings::class])
        ->post('/test-macros-scalar', function (MacroTestFormRequest $request) {
            return response()->json('ok');
        });

    $response = $this->postJson('/test-macros-scalar', [
        'email' => 'test@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertHasWarning('name', 'Name too short for display.');
});

class MacroTestFormRequest extends FormRequest
{
    use HasWarningRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
        ];
    }

    public function warningRules(): array
    {
        return [
            'name' => ['min:3'],
        ];
    }

    public function warningMessages(): array
    {
        return [
            'name.min' => 'Name too short for display.',
        ];
    }
}
