<?php

return [
    'environment' => env('SECUREPAY_ENVIRONMENT', 'sandbox'),
    'api_uid' => env('SECUREPAY_API_UID'),
    'auth_token' => env('SECUREPAY_AUTH_TOKEN'),
    'checksum_token' => env('SECUREPAY_CHECKSUM_TOKEN'),
];
