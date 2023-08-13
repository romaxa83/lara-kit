<?php

return [
    'cache' => [
        'durations' => [
            /*
             * Продолжительность в секундах
             */
            'has_active_package' => env('COMPANIES_HAS_ACTIVE_PACKAGE_CACHE_DURATION', 3600),
            'has_filled_questionnaire' => env('COMPANIES_HAS_FILLED_QUESTIONNAIRE_CACHE_DURATION', 3600),
            'is_approved' => env('COMPANIES_IS_APPROVED_CACHE_DURATION', 3600),
        ],
    ],
    'inactive' => [
        'keep-data-days' => env('KEEP_INACTIVE_COMPANY_DATA_DAYS', 180),
    ],
];
