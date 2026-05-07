<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus\Traits;

use Fridzema\ValidationPlus\WarningBag;
use Fridzema\ValidationPlus\WarningValidator;
use Illuminate\Contracts\Validation\Validator;

trait HasWarningRules
{
    /**
     * @return array<string, mixed>
     */
    public function warningRules(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function warningMessages(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function warningAttributes(): array
    {
        return [];
    }

    protected function getValidatorInstance(): Validator
    {
        $validator = parent::getValidatorInstance();

        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->evaluateWarningRules();
        });

        return $validator;
    }

    protected function evaluateWarningRules(): void
    {
        /** @var array<string, mixed> $warningRules */
        $warningRules = app()->call([$this, 'warningRules']);

        if ($warningRules === []) {
            return;
        }

        if ($this->isPrecognitive()) {
            $warningRules = $this->filterPrecognitiveRules($warningRules);

            if ($warningRules === []) {
                return;
            }
        }

        /** @var WarningValidator $warningValidator */
        $warningValidator = app(WarningValidator::class);

        /** @var array<string, string> $warningMessages */
        $warningMessages = app()->call([$this, 'warningMessages']);
        /** @var array<string, string> $warningAttributes */
        $warningAttributes = app()->call([$this, 'warningAttributes']);

        $warningBag = $warningValidator->validate(
            $this->validationData(),
            $warningRules,
            $warningMessages,
            $warningAttributes,
        );

        app(WarningBag::class)->merge($warningBag->getMessages());
    }
}
