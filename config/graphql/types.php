<?php

declare(strict_types=1);

// The types available in the application. You can then access it from the
// facade like this: GraphQL::type('user')
//
// Example:
//
// 'types' => [
//     'user' => 'App\GraphQL\Type\UserType'
// ]

return [
    App\GraphQL\Types\UploadType::class,

    App\GraphQL\Types\Enums\Messages\MessageKindEnumType::class,
    App\GraphQL\Types\Enums\Messages\AlertTargetEnumType::class,

    App\GraphQL\Types\Enums\Permissions\AdminPermissionEnum::class,
    App\GraphQL\Types\Localization\LanguageType::class,
    App\GraphQL\Types\Localization\TranslateType::class,

    // Permissions
    App\GraphQL\Types\Roles\RoleType::class,
    App\GraphQL\Types\Roles\RoleTranslationType::class,
    App\GraphQL\Types\Roles\PermissionType::class,
    App\GraphQL\Types\Roles\PermissionTranslationType::class,
    // Permissions input
    App\GraphQL\InputTypes\Permissions\RoleCreateInput::class,
    App\GraphQL\InputTypes\Permissions\RoleUpdateInput::class,
    App\GraphQL\InputTypes\Permissions\RoleTranslationInput::class,
    App\GraphQL\InputTypes\Permissions\PermissionUpdateInput::class,
    App\GraphQL\InputTypes\Permissions\PermissionTranslationInput::class,
    // Permissions enum
    App\GraphQL\Types\Enums\Permissions\GuardEnum::class,

    App\GraphQL\Types\Roles\RoleTranslateInputType::class,
    App\GraphQL\Types\Roles\GrantType::class,
    App\GraphQL\Types\Roles\GrantGroupType::class,

    // Auth
    App\GraphQL\Types\Auth\AuthTokenType::class,
    // Auth input
    App\GraphQL\InputTypes\Auth\ResetPasswordInput::class,
    App\GraphQL\InputTypes\Auth\ChangePasswordInput::class,

    // User
    App\GraphQL\Types\Users\UserType::class,
    App\GraphQL\Types\Users\UserProfileType::class,
    // User input
    App\GraphQL\InputTypes\Users\UserRegisterInput::class,
    App\GraphQL\InputTypes\Users\UserLoginInput::class,

    // Admin type
    App\GraphQL\Types\Admins\AdminType::class,
    App\GraphQL\Types\Admins\AdminLoginType::class,
    App\GraphQL\Types\Admins\AdminProfileType::class,
    // Admin input
    App\GraphQL\InputTypes\Admins\AdminInput::class,
    App\GraphQL\InputTypes\Admins\AdminLoginInput::class,

    // Translation input
    App\GraphQL\InputTypes\Localization\TranslationInput::class,
    App\GraphQL\InputTypes\Localization\TranslationDeleteInput::class,

    App\GraphQL\Types\Unions\Authenticatable::class,

    App\GraphQL\Types\Messages\ResponseMessageType::class,
    App\GraphQL\Types\Messages\AlertMessageType::class,

];
