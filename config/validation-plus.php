<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | API Warning Header
    |--------------------------------------------------------------------------
    |
    | The HTTP header name added to API responses when warnings are present.
    |
    */
    'header' => 'X-Validation-Warnings',

    /*
    |--------------------------------------------------------------------------
    | Inject Warnings Into JSON Responses
    |--------------------------------------------------------------------------
    |
    | When true, warnings are automatically merged into JSON response bodies
    | under the configured json_key when the ShareWarnings middleware is active.
    |
    */
    'inject_json' => true,

    /*
    |--------------------------------------------------------------------------
    | JSON Response Key
    |--------------------------------------------------------------------------
    |
    | The key used when injecting warnings into JSON response bodies.
    |
    */
    'json_key' => 'warnings',

    /*
    |--------------------------------------------------------------------------
    | Session Flash Key
    |--------------------------------------------------------------------------
    |
    | The session key used when flashing warnings for web requests.
    |
    */
    'session_key' => 'warnings',
];
