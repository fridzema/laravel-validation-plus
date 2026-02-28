<?php

declare(strict_types=1);

namespace Fridzema\ValidationPlus;

use Illuminate\Support\Facades\Validator;

final class WarningValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     */
    public function validate(array $data, array $rules, array $messages = [], array $attributes = []): WarningBag
    {
        if ($rules === []) {
            return new WarningBag;
        }

        $validator = Validator::make($data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            return new WarningBag($validator->errors()->getMessages());
        }

        return new WarningBag;
    }
}
