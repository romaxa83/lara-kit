<?php

namespace App\GraphQL\InputTypes\Admins;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

class AdminInput extends BaseInputType
{
    public const NAME = 'AdminInputType';

    public function fields(): array
    {
        return [
            'name' => [
                'type' => NonNullType::string(),
            ],
            'email' => [
                'type' => NonNullType::string(),
            ],
            'password' => [
                'type' => Type::string(),
            ],
            'role' => [
                'type' => Type::id(),
                'description' => 'ID RoleType'
            ],
            'phone' => [
                'type' => Type::string(),
            ],
            'lang' => [
                'type' => Type::string(),
            ],
        ];
    }
}
