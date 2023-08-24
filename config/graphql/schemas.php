<?php

declare(strict_types=1);

// The schemas for query and/or mutation. It expects an array of schemas to provide
// both the 'query' fields and the 'mutation' fields.
//
// You can also provide a middleware that will only apply to the given schema
//
// Example:
//
//  'schema' => 'default',
//
//  'schemas' => [
//      'default' => [
//          'query' => [
//              'users' => 'App\GraphQL\Query\UsersQuery'
//          ],
//          'mutation' => [
//
//          ]
//      ],
//      'user' => [
//          'query' => [
//              'profile' => 'App\GraphQL\Query\ProfileQuery'
//          ],
//          'mutation' => [
//
//          ],
//          'middleware' => ['auth'],
//      ],
//      'user/me' => [
//          'query' => [
//              'profile' => 'App\GraphQL\Query\MyProfileQuery'
//          ],
//          'mutation' => [
//
//          ],
//          'middleware' => ['auth'],
//      ],
//  ]

return [
    'default' => [
        'query' => [
            // Localization
            App\GraphQL\Queries\Common\Localization\LanguagesQuery::class,
            App\GraphQL\Queries\Common\Localization\TranslationsQuery::class,

            // User
            App\GraphQL\Queries\FrontOffice\Users\UserProfileQuery::class,
        ],
        'mutation' => [
            // Localization
            App\GraphQL\Mutations\Common\Localization\SetLanguageMutation::class,

            // Auth
            App\GraphQL\Mutations\FrontOffice\Auth\RegisterMutation::class,
            App\GraphQL\Mutations\FrontOffice\Auth\LoginMutation::class,
            App\GraphQL\Mutations\FrontOffice\Auth\LogoutMutation::class,
            App\GraphQL\Mutations\FrontOffice\Auth\TokenRefreshMutation::class,
            App\GraphQL\Mutations\FrontOffice\Auth\ForgotPasswordMutation::class,
            App\GraphQL\Mutations\FrontOffice\Auth\ResetPasswordMutation::class,

            // Verification phone
            App\GraphQL\Mutations\FrontOffice\Verification\Phone\RequestMutation::class,
            App\GraphQL\Mutations\FrontOffice\Verification\Phone\VerificationMutation::class,

            App\GraphQL\Mutations\FrontOffice\Users\UserEmailVerificationMutation::class,
            App\GraphQL\Mutations\FrontOffice\Users\UserResendVerificationMutation::class,
            App\GraphQL\Mutations\FrontOffice\Users\UserChangePasswordMutation::class,
        ],
        'subscription' => [
            App\GraphQL\Subscriptions\FrontOffice\Notifications\NotificationSubscription::class
        ],
        'middleware' => [],
        'method' => ['POST'],
        'execution_middleware' => null,
    ],
    'BackOffice' => [
        'query' => [
            // User
            App\GraphQL\Queries\BackOffice\Users\UsersQuery::class,

            // Admin
            App\GraphQL\Queries\BackOffice\Admins\AdminsQuery::class,
            App\GraphQL\Queries\BackOffice\Admins\AdminProfileQuery::class,

            // Permissions
            App\GraphQL\Queries\BackOffice\Permissions\RolesQuery::class,
            App\GraphQL\Queries\BackOffice\Permissions\PermissionsQuery::class,

            // Localization
            App\GraphQL\Queries\Common\Localization\LanguagesQuery::class,
            App\GraphQL\Queries\Common\Localization\TranslationsQuery::class,
            App\GraphQL\Queries\BackOffice\Localization\TranslationsQuery::class,
        ],
        'mutation' => [
            // Auth
            App\GraphQL\Mutations\BackOffice\Auth\LoginMutation::class,
            App\GraphQL\Mutations\BackOffice\Auth\LogoutMutation::class,
            App\GraphQL\Mutations\BackOffice\Auth\TokenRefreshMutation::class,
            App\GraphQL\Mutations\BackOffice\Auth\ForgotPasswordMutation::class,
            App\GraphQL\Mutations\BackOffice\Auth\ResetPasswordMutation::class,
            App\GraphQL\Mutations\BackOffice\Auth\CheckResetPasswordTokenMutation::class,

            // Admin
            App\GraphQL\Mutations\BackOffice\Admins\AdminCreateMutation::class,
            App\GraphQL\Mutations\BackOffice\Admins\AdminUpdateMutation::class,
            App\GraphQL\Mutations\BackOffice\Admins\AdminDeleteMutation::class,
            App\GraphQL\Mutations\BackOffice\Admins\AdminChangePasswordMutation::class,
            App\GraphQL\Mutations\BackOffice\Admins\Avatar\AvatarUploadMutation::class,
            App\GraphQL\Mutations\BackOffice\Admins\Avatar\AvatarDeleteMutation::class,

            // User
            App\GraphQL\Mutations\BackOffice\Users\UserDeleteMutation::class,

            // Localization
            App\GraphQL\Mutations\Common\Localization\SetLanguageMutation::class,
            App\GraphQL\Mutations\BackOffice\Localization\TranslationCreateOrUpdateMutation::class,
            App\GraphQL\Mutations\BackOffice\Localization\TranslationDeleteMutation::class,

            // Permissions
            App\GraphQL\Mutations\BackOffice\Permission\RoleCreateMutation::class,
            App\GraphQL\Mutations\BackOffice\Permission\RoleUpdateMutation::class,
            App\GraphQL\Mutations\BackOffice\Permission\RoleDeleteMutation::class,
            App\GraphQL\Mutations\BackOffice\Permission\PermissionUpdateMutation::class,

            // Verification phone
            App\GraphQL\Mutations\BackOffice\Verification\Phone\RequestMutation::class,
            App\GraphQL\Mutations\BackOffice\Verification\Phone\VerificationMutation::class,

        ],
        'subscription' => [
            App\GraphQL\Subscriptions\BackOffice\Notifications\NotificationSubscription::class,
        ],
        'middleware' => [

        ],
        'method' => ['POST'],
        'execution_middleware' => null,
    ],
];
