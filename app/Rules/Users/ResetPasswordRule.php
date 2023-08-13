<?php

namespace App\Rules\Users;

use App\Modules\Admins\Models\Admin;
use App\Services\Users\UserVerificationService;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class ResetPasswordRule implements Rule
{
    private string $message = '';

    /** @throws Exception */
    public function passes($attribute, $value): bool
    {
        $data = app(UserVerificationService::class)->decryptTokenForEmailReset($value);

        if (now()->parse($data['time'])->addDay()->timestamp < time()) {
            $this->message = __('validation.custom.reset_password.time');

            return false;
        }

        $user = Admin::query()->find($data['id']);

        if (!$user) {
            $this->message = __('validation.custom.reset_password.user');

            return false;
        }

        if ((int)$user->getEmailVerificationCode() !== $data['code']) {
            $this->message = __('validation.custom.reset_password.code');

            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->message;
    }
}
