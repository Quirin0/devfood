<?php

$explicit = env('CORS_ALLOWED_ORIGINS');

if ($explicit !== null && $explicit !== '') {
    $origins = $explicit;
} else {
    $appUrl = rtrim((string) env('APP_URL', ''), '/');
    $origins = $appUrl !== '' ? $appUrl : '*';
}

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $origins === '*'
        ? ['*']
        : array_values(array_filter(array_map('trim', explode(',', $origins)))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
