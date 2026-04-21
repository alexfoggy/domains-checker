<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'ahrefs' => [
        'api_key' => env('AHREFS_API_KEY'),
    ],

    'hunter' => [
        'api_key' => env('HUNTER_API_KEY'),
        'base_url' => env('HUNTER_IO_BASE_URL', 'https://api.hunter.io'),
        'leads_list_id' => env('HUNTER_IO_LEADS_LIST_ID'),
        'timeout' => (int)env('HUNTER_IO_TIMEOUT', 30),
        'source' => env('HUNTER_IO_SOURCE', 'Totem'),
        'campaigns' => [
            'one' => 825245,
            'two' => 825255,
            'three' => 825257,
            'four' => 825258,
            'five' => 825259,
            'six' => 825260,
            'seven' => 825261,
            'eight' => 825263,
        ]
    ],
];
