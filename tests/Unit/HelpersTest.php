<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\WarningBag;

it('warnings() returns WarningBag instance', function (): void {
    expect(warnings())->toBeInstanceOf(WarningBag::class);
});

it('warnings() returns same scoped instance as app(WarningBag::class)', function (): void {
    expect(warnings())->toBe(app(WarningBag::class));
});
