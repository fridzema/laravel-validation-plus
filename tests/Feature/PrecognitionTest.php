<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Middleware\ShareWarnings;
use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::middleware([HandlePrecognitiveRequests::class, ShareWarnings::class])
        ->post('/test-precognition', function (PrecognitionFormRequest $request) {
            return response()->json(['status' => 'ok']);
        });
});

it('returns warnings header on precognitive request when warnings are triggered', function (): void {
    $response = $this->withPrecognition()
        ->withHeader('Precognition-Validate-Only', 'name,email')
        ->postJson('/test-precognition', [
            'email' => 'test@example.com',
            'name' => 'Jo',
        ]);

    $response->assertSuccessfulPrecognition();
    expect($response->headers->has('X-Validation-Warnings'))->toBeTrue();
    expect($response->headers->has('X-Validation-Warnings-Data'))->toBeTrue();

    $warningData = json_decode($response->headers->get('X-Validation-Warnings-Data'), true);
    expect($warningData)->toHaveKey('name');
    expect($warningData['name'])->toContain('Short names may cause display issues.');
});

it('returns no warnings header on precognitive request when no warnings', function (): void {
    $response = $this->withPrecognition()
        ->withHeader('Precognition-Validate-Only', 'name,email')
        ->postJson('/test-precognition', [
            'email' => 'test@example.com',
            'name' => 'Jonathan',
        ]);

    $response->assertSuccessfulPrecognition();
    expect($response->headers->has('X-Validation-Warnings'))->toBeFalse();
    expect($response->headers->has('X-Validation-Warnings-Data'))->toBeFalse();
});

it('returns 422 on precognitive request when validation fails', function (): void {
    $response = $this->withPrecognition()
        ->withHeader('Precognition-Validate-Only', 'email')
        ->postJson('/test-precognition', [
            'email' => '',
            'name' => 'Jo',
        ]);

    $response->assertUnprocessable();
    expect($response->headers->has('X-Validation-Warnings'))->toBeFalse();
});

it('filters warning rules by Precognition-Validate-Only header', function (): void {
    $response = $this->withPrecognition()
        ->withHeader('Precognition-Validate-Only', 'email')
        ->postJson('/test-precognition', [
            'email' => 'test@example.com',
            'name' => 'Jo', // short name, but name not in Validate-Only
        ]);

    $response->assertSuccessfulPrecognition();
    // name warning should NOT appear because name is not in Precognition-Validate-Only
    expect($response->headers->has('X-Validation-Warnings'))->toBeFalse();
});

class PrecognitionFormRequest extends FormRequest
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
