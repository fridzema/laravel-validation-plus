<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus\Traits;

use Fridzema\ValidationPlus\WarningBag;
use Fridzema\ValidationPlus\WarningValidator;

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

    protected function passedValidation(): void
    {
        $warningRules = $this->warningRules();

        if ($warningRules === []) {
            return;
        }

        /** @var WarningValidator $warningValidator */
        $warningValidator = app(WarningValidator::class);

        $warningBag = $warningValidator->validate(
            $this->validated(),
            $warningRules,
            $this->warningMessages(),
        );

        app(WarningBag::class)->merge($warningBag->getMessages());
    }
}
