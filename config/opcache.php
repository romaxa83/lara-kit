<?php

return [
    'url' => env('OPCACHE_URL', config('app.url')),
    'prefix' => 'opcache-api',
    'verify_ssl' => false,
    'verify' => true,
    'verify_host' => 0, // 0 for disabled

    'timeout' => env('OPCACHE_COMPILE_TIME_LIMIT', 60),

    'headers' => [],
    'directories' => [
        base_path('app'),
        base_path('bootstrap'),
//        base_path('public'),
        base_path('resources'),
        base_path('routes'),
        base_path('storage/framework/views'),
        base_path('vendor'),
    ],
    'exclude' => [
        'test',
        'Test',
        'tests',
        'Tests',
        'stub',
        'Stub',
        'stubs',
        'Stubs',
        'dumper',
        'Dumper',
        'Autoload',
        'ezyang',
//        'symfony',
//        'doctrine/cache/lib/Doctrine',
    ]
];
