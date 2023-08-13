<?php

namespace App\Services\Users;

use App\Exceptions\Users\EmailAlreadyVerifiedException;
use App\Modules\User\Models\User;
use App\Notifications\Users\UserEmailVerification;
use App\Traits\VerificationCodeGenerator;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class UserVerificationService
{
    use VerificationCodeGenerator;

    /**
     * @throws Exception
     */
    public function verifyEmail(User $user): bool
    {
        $this->checkEmailVerified($user);

        $this->fillEmailVerificationCode($user);

        Notification::route('mail', (string)$user->getEmail())
            ->notify(
                (new UserEmailVerification($user))
                    ->locale(app()->getLocale())
            );

        return true;
    }

    /**
     * @throws Exception
     */
    protected function checkEmailVerified(User $user): void
    {
        if ($user->isEmailVerified()) {
            throw new EmailAlreadyVerifiedException(__('exceptions.email_already_verified'));
        }
    }

    /**
     * @throws Exception
     */
    public function fillEmailVerificationCode(User $user): void
    {
        $user->email_verification_code = $this->generateVerificationCode();
        $user->save();
    }

    /**
     * @throws Exception
     */
    public function verifyEmailByCode(Authenticatable|User $user, string $code): bool
    {
        $this->checkEmailVerified($user);

        if ($user->email_verification_code !== $code) {
            return false;
        }

        $user->email_verified_at = now();

        $this->cleanEmailVerificationCode($user);

        return true;
    }

    public function cleanEmailVerificationCode(User $user): void
    {
        $user->email_verification_code = null;
        $user->save();
    }

    /**
     * @throws Exception
     */
    public function decryptTokenForEmailReset(string $token): array
    {
        try {
            return json_decode(
                Crypt::decryptString($token),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getLinkForEmailReset(User $user, string $link): string
    {
        return trim($link, '/') . '/' . $this->encryptTokenForEmailReset($user);
    }

    /**
     * @throws Exception
     */
    public function encryptTokenForEmailReset(User $user): string
    {
        $this->fillEmailVerificationCode($user);

        try {
            return Crypt::encryptString(
                json_encode(
                    [
                        'id' => $user->id,
                        'time' => time(),
                        'code' => (int)$user->getEmailVerificationCode()
                    ],
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
