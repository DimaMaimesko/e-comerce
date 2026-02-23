<?php

return [
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY', 'sk_test_xxxxx'),
    ],
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', 'xxx'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', 'yyy'),
    ],
    'crypto' => [
        'wallet_address' => env('CRYPTO_WALLET', '0x1234...'),
    ],
];
