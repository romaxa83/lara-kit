<?php

namespace App\GraphQL\Types\Users;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\Localization\LanguageType;
use App\GraphQL\Types\NonNullType;
use App\GraphQL\Types\Roles\PermissionType;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\User\Models\User;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;

class UserProfileType extends BaseType
{
    public const NAME = 'UserProfileType';
    public const MODEL = User::class;

    public function fields(): array
    {
        return [
            'id' => [
                'type' => NonNullType::id()
            ],
            'name' => [
                'type' => NonNullType::string(),
            ],
            'email' => [
                'type' => NonNullType::string(),
                'resolve' => static fn(User $m) => $m->getEmail()
            ],
            'email_verified' => [
                'type' => Type::boolean(),
                'resolve' => static fn(User $m) => $m->isEmailVerified()
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => static fn(User $m) => $m->getPhone()
            ],
            'phone_verified' => [
                'type' => Type::boolean(),
                'resolve' => static fn(User $m) => $m->isPhoneVerified()
            ],
            'role' => [
                'is_relation' => false,
                'type' => RoleType::nonNullType(),
                'resolve' => static fn(User $m) => $m->role,
            ],
            'roles' => [
                'type' => RoleType::list(),
            ],
            'lang' => [
                'type' => NonNullType::string(),
            ],
            'language' => [
                'type' => LanguageType::type(),
            ],
            'permissions' => [
                /** @see UserProfileType::resolvePermissionsField() */
                'type' => PermissionType::list(),
                'is_relation' => false,
            ],
        ];
    }

    protected function resolvePermissionsField(User $root): Collection
    {
        return $root->getAllPermissions();
    }

    protected function resolveAlertsField(User $root): Collection
    {
        return $root->getAlerts();
    }
}
