<?php

return [
    'ip-access' => [
        'list' => explode(
            ',',
            env('SECURITY_IP_ACCESS_LIST', '127.0.0.1')
        ),
        'enabled' => env('SECURITY_IP_ACCESS_ENABLED', false),
        'cache' => [
            'duration' => env('SECURITY_IP_ACCESS_CACHE_DURATION', 3600),
        ],
    ],
];
