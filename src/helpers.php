<?php

declare(strict_types=1);

use Fridzema\ValidationPlus\WarningBag;

if (! function_exists('warnings')) {
    function warnings(): WarningBag
    {
        return app(WarningBag::class);
    }
}
