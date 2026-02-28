<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Middleware\ShareWarnings;
use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::middleware(['web', ShareWarnings::class])
        ->post('/test-form-request', function (TestFormRequest $request) {
            return response()->json(['status' => 'ok']);
        });

    Route::middleware([ShareWarnings::class])
        ->post('/test-api', function (TestFormRequest $request) {
            return response()->json(['status' => 'ok']);
        });
});

it('allows request through when validation passes but generates warnings', function (): void {
    $response = $this->postJson('/test-api', [
        'email' => 'exists@test.com',
        'name' => 'Jo',
    ]);

    $response->assertOk();
    $response->assertJsonPath('status', 'ok');
    $response->assertJsonStructure(['warnings']);
});

it('blocks request when validation errors occur regardless of warnings', function (): void {
    $response = $this->postJson('/test-api', [
        'email' => '', // required rule fails
        'name' => 'Jo',
    ]);

    $response->assertUnprocessable();
});

it('returns no warnings when warning rules pass', function (): void {
    $response = $this->postJson('/test-api', [
        'email' => 'good@test.com',
        'name' => 'Jonathan',
    ]);

    $response->assertOk();
    expect($response->headers->has(config('validation-plus.header')))->toBeFalse();
});

class TestFormRequest extends FormRequest
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
            'name.min' => 'Short names may cause display issues.',
        ];
    }
}
