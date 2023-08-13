<?php

namespace App\GraphQL\Mutations\BackOffice\Auth;

use App\GraphQL\Types\Admins\AdminLoginType;
use App\Services\Auth\AdminPassportService;
use Core\GraphQL\Mutations\BaseTokenRefreshMutation;
use GraphQL\Type\Definition\Type;

class TokenRefreshMutation extends BaseTokenRefreshMutation
{
    public const NAME = 'refreshToken';

    public function __construct(
        protected AdminPassportService $passportService
    ) {
    }

    public function type(): Type
    {
        return AdminLoginType::type();
    }
}
