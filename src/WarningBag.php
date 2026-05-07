<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus;

use Illuminate\Support\MessageBag;

final class WarningBag extends MessageBag
{
    private bool $resolvedFromSession = false;

    public function markResolvedFromSession(): static
    {
        $this->resolvedFromSession = true;

        return $this;
    }

    public function wasResolvedFromSession(): bool
    {
        return $this->resolvedFromSession;
    }

    public function addGlobal(string $message): static
    {
        $this->add('__global__', $message);

        return $this;
    }

    public function hasGlobal(): bool
    {
        return $this->has('__global__');
    }
}
