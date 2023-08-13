<?php

namespace App\Modules\Auth\Traits;

use App\Exceptions\Auth\EmailAlreadyVerifiedException;
use App\Modules\Utils\Tokenizer\Traits\CodeGenerator;
use App\Modules\Utils\Tokenizer\Traits\EmailCryptoToken;
use Core\Models\BaseAuthenticatable;
use Exception;

trait EmailVerificationTrait
{
    use CodeGenerator;
    use EmailCryptoToken;

    public function encryptEmailToken(BaseAuthenticatable $user): string
    {
        $this->fillEmailVerificationCode($user);

        return $this->encrypt($user);
    }

    /** @throws Exception */
    public function fillEmailVerificationCode(BaseAuthenticatable $user): void
    {
        $user->email_verification_code = $this->verificationCodeForEmail();
        $user->save();
    }

    /** @throws Exception */
    public function verifyEmailByCode(BaseAuthenticatable $user, string $code): bool
    {
        $this->assertEmailNotVerified($user);

        if ($user->email_verification_code !== $code) {
            return false;
        }

        $user->email_verified_at = now();

        $this->cleanEmailVerificationCode($user);

        return true;
    }

    /** @throws Exception */
    protected function assertEmailNotVerified(BaseAuthenticatable $user): void
    {
        if ($user->isEmailVerified()) {
            throw new EmailAlreadyVerifiedException(__('Email already verified!'));
        }
    }

    public function cleanEmailVerificationCode(BaseAuthenticatable $user): void
    {
        $user->email_verification_code = null;
        $user->save();
    }
}



