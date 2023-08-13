<?php

return [
    /**
     * Limit by user or ip
     */
    'limit_by' => env('ROUTES_LIMIT_BY', 'ip'),
    'rates' => [
        'api' => env('ROUTES_RATES_API', 300),
    ],
];
