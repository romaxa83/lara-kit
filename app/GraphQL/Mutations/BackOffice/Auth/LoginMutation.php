<?php

namespace App\GraphQL\Mutations\BackOffice\Auth;

use App\GraphQL\InputTypes\Admins\AdminLoginInput;
use App\GraphQL\Types\Admins\AdminLoginType;
use App\Modules\Auth\Rules\LoginAdmin;
use App\Services\Auth\AdminPassportService;
use Core\GraphQL\Mutations\BaseLoginMutation;
use GraphQL\Type\Definition\Type;

class LoginMutation extends BaseLoginMutation
{
    public const NAME = 'login';

    public function __construct(
        protected AdminPassportService $passportService,
    ) {
        $this->setAdminGuard();
    }
    public function args(): array
    {
        return [
            'input' => AdminLoginInput::nonNullType(),
        ];
    }

    protected function rules(array $args = []): array
    {
        return [
                'input.email' => ['required', 'string', 'email'],
                'input.password' => ['required', 'string',  new LoginAdmin($args['input'])],
            ] + $this->rememberMeRule('input.');
    }
}

