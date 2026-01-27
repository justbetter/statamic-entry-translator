<?php

return [
    'queue' => 'default',

    'service' => 'deepl',

    'services' => [
        'deepl' => [
            'translator' => JustBetter\EntryTranslator\Translators\DeeplTranslator::class,

            'auth_key' => env('DEEPL_AUTH_KEY'),

            'server_url' => env('DEEPL_SERVER_URL', null),
        ],
    ],

    'excluded_handles' => [
        'id',
        'type',
    ],

    'excluded_types' => [
        'select',
        'assets',
        'entries',
    ],
];
