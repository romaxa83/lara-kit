<?php

namespace App\GraphQL\InputTypes\Users;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use App\Traits\Auth\CanRememberMe;
use GraphQL\Type\Definition\Type;

class UserRegisterInput extends BaseInputType
{
    use CanRememberMe;

    public const NAME = 'UserRegisterInputType';

    public function fields(): array
    {
        return array_merge(
            [
                'name' => [
                    'type' => NonNullType::string(),
                ],
                'email' => [
                    'type' => NonNullType::string(),
                ],
                'password' => [
                    'type' => NonNullType::string(),
                ],
                'password_confirmation' => [
                    'type' => NonNullType::string(),
                ],
                'phone' => [
                    'type' => Type::string(),
                ],
            ],
            $this->rememberMeArg()
        );
    }
}
