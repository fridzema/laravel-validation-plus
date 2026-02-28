<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\Tests\TestCase;
use Fridzema\ValidationPlus\Traits\HasWarningRules;
use Illuminate\Foundation\Http\FormRequest;

uses(TestCase::class)->in(__DIR__);

function createFormRequest(
    array $data = [],
    array $rules = [],
    array $warningRules = [],
    array $warningMessages = [],
): FormRequest {
    $requestClass = new class extends FormRequest
    {
        use HasWarningRules;

        public static array $testRules = [];

        public static array $testWarningRules = [];

        public static array $testWarningMessages = [];

        public function rules(): array
        {
            return static::$testRules;
        }

        public function warningRules(): array
        {
            return static::$testWarningRules;
        }

        public function warningMessages(): array
        {
            return static::$testWarningMessages;
        }

        public function authorize(): bool
        {
            return true;
        }
    };

    $requestClass::$testRules = $rules;
    $requestClass::$testWarningRules = $warningRules;
    $requestClass::$testWarningMessages = $warningMessages;

    $request = $requestClass::create('/', 'POST', $data);
    $request->setContainer(app());

    return $request;
}
