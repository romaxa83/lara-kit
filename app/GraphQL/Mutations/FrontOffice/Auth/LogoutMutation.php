<?php

namespace App\GraphQL\Mutations\FrontOffice\Auth;

use App\Services\Auth\UserPassportService;
use Core\GraphQL\Mutations\BaseLogoutMutation;

class LogoutMutation extends BaseLogoutMutation
{
    public const NAME = 'logout';

    public function __construct(
        protected UserPassportService $passportService
    ) {
    }
}

