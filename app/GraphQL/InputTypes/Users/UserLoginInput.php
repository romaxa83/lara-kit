<?php

namespace App\GraphQL\InputTypes\Users;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use App\Traits\Auth\CanRememberMe;

class UserLoginInput extends BaseInputType
{
    use CanRememberMe;

    public const NAME = 'UserLoginInputType';

    public function fields(): array
    {
        return array_merge(
            [
                'email' => [
                    'type' => NonNullType::string(),
                ],
                'password' => [
                    'type' => NonNullType::string(),
                ],
            ],
            $this->rememberMeArg()
        );
    }
}

