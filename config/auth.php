<?php

use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;

return [

    'defaults' => [
        'guard' => User::GUARD,
        'passwords' => 'users',
    ],

    'guards' => [
        User::GUARD => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
        Admin::GUARD => [
            'driver' => 'passport',
            'provider' => 'admins',
            'hash' => false,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    'oauth_client' => [
        'users' => [
            'id' => env('OAUTH_USERS_CLIENT_ID'),
            'secret' => env('OAUTH_USERS_CLIENT_SECRET'),
        ],

        'admins' => [
            'id' => env('OAUTH_ADMINS_CLIENT_ID'),
            'secret' => env('OAUTH_ADMINS_CLIENT_SECRET'),
        ],
    ],

    'reset_password_token_life' => env('RESET_PASSWORD_TOKEN_LIFETIME', 60), //60 min

    'oauth_tokens_expire_in' => env('ACCESS_TOKEN_LIFETIME', 15), //15 min
    'oauth_refresh_tokens_expire_in' => env('REFRESH_TOKEN_LIFETIME', 60), //60 min
    'oauth_remembered_refresh_tokens_expire_in' => env('REMEMBERED_REFRESH_TOKEN_LIFETIME', 60 * 24 * 30), //1 month
    'oauth_personal_access_tokens_expire_in' => env('PERSONAL_ACCESS_TOKENS_EXPIRE_IN', 1440),

];
