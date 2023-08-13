<?php

namespace App\GraphQL\Mutations\FrontOffice\Auth;

use App\GraphQL\InputTypes\Users\UserLoginInput;
use App\Modules\Auth\Rules\LoginUser;
use App\Services\Auth\UserPassportService;
use Core\GraphQL\Mutations\BaseLoginMutation;

class LoginMutation extends BaseLoginMutation
{
    public const NAME = 'login';

    public function __construct(
        protected UserPassportService $passportService
    )
    {}

    public function args(): array
    {
        return [
            'input' => UserLoginInput::nonNullType(),
        ];
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.email' => ['required', 'string', 'email'],
            'input.password' => ['required', 'string',  new LoginUser($args['input'])],
        ] + $this->rememberMeRule('input.');
    }
}
