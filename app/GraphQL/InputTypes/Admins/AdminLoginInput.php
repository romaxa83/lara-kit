<?php

namespace App\GraphQL\InputTypes\Admins;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use App\Traits\Auth\CanRememberMe;

class AdminLoginInput extends BaseInputType
{
    use CanRememberMe;

    public const NAME = 'AdminLoginInputType';

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


