<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\BackOffice\Auth;

use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Auth\Services\VerificationService;
use Core\GraphQL\Mutations\BaseForgotPasswordMutation;

class ForgotPasswordMutation extends BaseForgotPasswordMutation
{
    public const NAME = 'forgotPassword';

    public function __construct(
        protected AdminRepository $repo,
        protected VerificationService $service
    )
    {}
}

