<?php

return [
    'default' => [
        'pagination' => [
            'per_page' => env('DEFAULT_PAGINATION_PER_PAGE', 15),
            'max_per_page' => env('PAGINATION_MAX_PER_PAGE', 1500),
        ],
    ],
    'employees' => [
        'pagination' => [
            'per_page' => env('QUERIES_EMPLOYEES_PAGINATION_PER_PAGE', 15),
            'admin_per_page' => env('QUERIES_EMPLOYEES_ADMIN_PAGINATION_PER_PAGE', 15),
        ],
    ],
    'roles' => [
        'pagination' => [
            'per_page' => env('QUERIES_EMPLOYEE_ROLES_PAGINATION_PER_PAGE', 15),
            'admin_per_page' => env('QUERIES_EMPLOYEE_ROLES_ADMIN_PAGINATION_PER_PAGE', 15),
        ],
    ],
    'locales' => [
        'pagination' => [
            'per_page' => env('QUERIES_LOCALES_PAGINATION_PER_PAGE', 15),
        ],
    ],
    'companies' => [
        'pagination' => [
            'admin_per_page' => env('QUERIES_COMPANIES_ADMIN_PAGINATION_PER_PAGE', 15),
        ],
    ],

    'country-codes' => [
        'pagination' => [
            'admin_per_page' => env('COUNTRY_CODES_ADMIN_PAGINATION_PER_PAGE', 15),
        ],
    ],

    'subscriptions' => [
        'pagination' => [
            'per_page' => env('SUBSCRIPTIONS_PAGINATION_PER_PAGE', 30),
        ],
    ],

    'localization' => [
        'translates' => [
            'cache' => env('QUERIES_TRANSLATES_CACHE', 3600),
        ],

        'translates_filterable' => [
            'limit' => env('QUERIES_TRANSLATES_FILTERABLE_LIMIT', 50),
        ],

        'languages' => [
            'cache' => env('QUERIES_LANGUAGES_CACHE', 3600),
        ],

        'locales' => [
            'cache' => env('QUERIES_LOCALES_CACHE', 3600),
        ],
    ],
    'transaction' => [
        'balance' => [
            'key' => 'BALANCE_CACHE',
            'limit' => env('BALANCE_CACHE_LIMIT', 86400),
        ],
    ],
];
