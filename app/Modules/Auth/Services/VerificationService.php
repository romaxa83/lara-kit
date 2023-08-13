<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Traits\EmailVerificationTrait;
use Core\Models\BaseAuthenticatable;

class VerificationService
{
    use EmailVerificationTrait;

    public function getLinkForPasswordReset(BaseAuthenticatable $user): string
    {
        return trim(config('front-routes.forgot-password'), '/') . '?token=' . $this->encryptEmailToken($user);
    }
}


