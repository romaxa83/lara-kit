<?php

namespace App\GraphQL\InputTypes\Auth;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;

class ChangePasswordInput extends BaseInputType
{
    public const NAME = 'ChangePasswordInputType';

    public function fields(): array
    {
        return [
            'current' => [
                'type' => NonNullType::string(),
            ],
            'password' => [
                'type' => NonNullType::string(),
            ],
            'password_confirmation' => [
                'type' => NonNullType::string(),
            ],
        ];
    }
}
