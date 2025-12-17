<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudlinker Organisation ID
    |--------------------------------------------------------------------------
    |
    | Your Cloudlinker organisation ID. This is used as the username for
    | Basic Authentication with the Cloudlinker API.
    |
    */

    'organisation_id' => env('CLOUDLINKER_ORG_ID'),

    /*
    |--------------------------------------------------------------------------
    | Cloudlinker API Key
    |--------------------------------------------------------------------------
    |
    | Your Cloudlinker API key. This is used as the password for
    | Basic Authentication with the Cloudlinker API.
    |
    */

    'api_key' => env('CLOUDLINKER_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cloudlinker API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Cloudlinker API. You typically don't need to
    | change this unless you're using a custom or staging environment.
    |
    */

    'base_url' => env('CLOUDLINKER_URL', 'https://cloudlinker.eu/api'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests. Increase this value if you're
    | experiencing timeout issues with large print jobs.
    |
    */

    'timeout' => env('CLOUDLINKER_TIMEOUT', 30),

];
