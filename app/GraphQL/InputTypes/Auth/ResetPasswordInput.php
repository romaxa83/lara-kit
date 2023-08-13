<?php

namespace App\GraphQL\InputTypes\Auth;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;

class ResetPasswordInput extends BaseInputType
{
    public const NAME = 'ResetPasswordInputType';

    public function fields(): array
    {
        return [
            'token' => [
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
