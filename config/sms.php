<?php

return [
    //"array"
    'driver' => env('SMS_DRIVER', 'array'),

    'drivers' => [],

    'enable_sender' => env('ENABLE_SMS_SENDER', false),

    // верефикация
    'verify' => [
        'code_length' => 4,                 // длина смс-кода
        'sms_token_expired' => 60 * 3,      // 3 min
    ]
];
