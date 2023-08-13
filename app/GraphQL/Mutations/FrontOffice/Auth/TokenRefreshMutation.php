<?php

namespace App\GraphQL\Mutations\FrontOffice\Auth;

use App\GraphQL\Types\Auth\AuthTokenType;
use App\Services\Auth\UserPassportService;
use Core\GraphQL\Mutations\BaseTokenRefreshMutation;
use GraphQL\Type\Definition\Type;

class TokenRefreshMutation extends BaseTokenRefreshMutation
{
    public const NAME = 'refreshToken';

    public function __construct(
        protected UserPassportService $passportService
    ) {
    }

    public function type(): Type
    {
        return AuthTokenType::type();
    }
}
