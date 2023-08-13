<?php

namespace App\GraphQL\Mutations\BackOffice\Auth;

use App\Services\Auth\AdminPassportService;
use Core\GraphQL\Mutations\BaseLogoutMutation;

class LogoutMutation extends BaseLogoutMutation
{
    public const NAME = 'logout';

    public function __construct(
        protected AdminPassportService $passportService
    ) {
        $this->setAdminGuard();
    }
}
