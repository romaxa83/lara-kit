<?php

use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use Core\Services\Permissions\Filters\UserEmailVerifiedPermissionFilter;

return [

    'permissions_disable' => env('PERMISSION_DISABLE', false),

    'matrix' => [

        Admin::GUARD => [
            'groups' => [
                App\Permissions\Admins\AdminPermissionsGroup::class => [
                    App\Permissions\Admins\AdminListPermission::class,
                    App\Permissions\Admins\AdminCreatePermission::class,
                    App\Permissions\Admins\AdminUpdatePermission::class,
                    App\Permissions\Admins\AdminDeletePermission::class,
                ],

                App\Permissions\Localization\Translation\TranslationPermissionGroup::class => [
                    App\Permissions\Localization\Translation\TranslationListPermission::class,
                    App\Permissions\Localization\Translation\TranslationUpdatePermission::class,
                    App\Permissions\Localization\Translation\TranslationDeletePermission::class,
                ],

                App\Permissions\Roles\RolePermissionsGroup::class => [
                    App\Permissions\Roles\RoleListPermission::class,
                    App\Permissions\Roles\RoleCreatePermission::class,
                    App\Permissions\Roles\RoleUpdatePermission::class,
                    App\Permissions\Roles\RoleDeletePermission::class,
                ],

                App\Permissions\Users\UserPermissionsGroup::class => [
                    App\Permissions\Users\UserListPermission::class,
                    App\Permissions\Users\UserCreatePermission::class,
                    App\Permissions\Users\UserUpdatePermission::class,
                    App\Permissions\Users\UserDeletePermission::class,
                    App\Permissions\Users\UserRestorePermission::class,
                ],
            ],
        ],

        \App\Modules\User\Models\User::GUARD => [
            'groups' => [
                App\Permissions\Roles\RolePermissionsGroup::class => [
                    App\Permissions\Roles\RoleListPermission::class,
                ],
            ],
        ],
    ],
    'filters' => [
        UserEmailVerifiedPermissionFilter::class => [],
    ],
    'filter_enabled' => env('PERMISSION_FILTER_ENABLED', true),

    /*
     * Описывает зависимости между разрешениями
     * Например: Если пользователь может создавать других пользователей, у него должен быть доступ к списку возможных ролей
     */
    'relations' => [
        User::GUARD => [
        ],
    ],
];
