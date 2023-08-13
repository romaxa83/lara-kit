<?php

namespace App\GraphQL\Types\Users;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\User\Models\User;
use Core\GraphQL\Fields\PermissionField;
use GraphQL\Type\Definition\Type;

class UserType extends BaseType
{
    public const NAME = 'UserType';
    public const MODEL = User::class;

    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
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
                    'resolve' => static fn(User $m) => $m->phone?->phone->getValue()
                ],
                'phone_verified' => [
                    'type' => Type::boolean(),
                    'resolve' => static fn(User $m) => $m->isPhoneVerified()
                ],
                'lang' => [
                    'type' => Type::string(),
                ],
                'permission' => PermissionField::class,
                'roles' => [
                    'type' => Type::listOf(RoleType::type()),
                ],
                'role' => [
                    'is_relation' => false,
                    'type' => RoleType::nonNullType(),
                    'resolve' => static fn(User $m) => $m->role
                ],
            ]
        );
    }
}
