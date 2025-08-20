<?php

return [
    'default' => env('CURRENCY_DEFAULT', 'ARS'),
    'rates' => [
        'ARS' => 1,
        'USD' => env('CURRENCY_RATE_USD', 0.0011),
        'EUR' => env('CURRENCY_RATE_EUR', 0.0010),
    ],
];

