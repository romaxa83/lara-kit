<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\FrontOffice\Auth;

use App\Modules\Auth\Services\VerificationService;
use App\Modules\User\Repositories\UserRepository;
use Core\GraphQL\Mutations\BaseForgotPasswordMutation;

class ForgotPasswordMutation extends BaseForgotPasswordMutation
{
    public const NAME = 'forgotPassword';

    public function __construct(
        protected UserRepository $repo,
        protected VerificationService $service
    )
    {}
}


