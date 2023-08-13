<?php

namespace App\GraphQL\Types\Admins;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\Admin\Models\Admin;
use GraphQL\Type\Definition\Type;

class AdminType extends BaseType
{
    public const NAME = 'AdminType';

    public const MODEL = Admin::class;

    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                'name' => [
                    'type' => NonNullType::string()
                ],
                'email' => [
                    'type' => NonNullType::string(),
                ],
                'email_verified' => [
                    'type' => Type::boolean(),
                    'resolve' => static fn(Admin $m) => $m->isEmailVerified()
                ],
                'phone' => [
                    'type' => Type::string(),
                    'resolve' => static fn(Admin $m) => $m->phone?->phone->getValue()
                ],
                'phone_verified' => [
                    'type' => Type::boolean(),
                    'resolve' => static fn(Admin $m) => $m->isPhoneVerified()
                ],
                'role' => [
                    'is_relation' => false,
                    'type' => RoleType::nonNullType(),
                    'resolve' => static fn(Admin $m) => $m->role
                ],
                'roles' => [
                    'type' => RoleType::list(),
                ],
                'lang' => [
                    'type' => NonNullType::string(),
                ],
            ]
        );
    }
}
