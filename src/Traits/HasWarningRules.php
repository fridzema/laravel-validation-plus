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
     * @return \Closure(\Illuminate\Contracts\Validation\Validator): void
     */
    public function after(): \Closure
    {
        return function (Validator $validator): void {
            if ($validator->messages()->isNotEmpty()) {
                return;
            }

            $this->evaluateWarningRules();
        };
    }

    private function evaluateWarningRules(): void
    {
        $warningRules = $this->warningRules();

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

        $warningBag = $warningValidator->validate(
            $this->validationData(),
            $warningRules,
            $this->warningMessages(),
        );

        app(WarningBag::class)->merge($warningBag->getMessages());
    }
}
