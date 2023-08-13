<?php

namespace App\Traits\Auth;

use App\Entities\Auth\EmailTokenEntity;
use App\Modules\Utils\Tokenizer\Exceptions\TokenDecryptException;
use App\Modules\Utils\Tokenizer\Exceptions\TokenEncryptException;
use Carbon\CarbonImmutable;
use Core\Models\BaseAuthenticatable;
use Exception;
use Illuminate\Support\Facades\Crypt;

trait EmailCryptToken
{
    /**
     * @throws Exception
     * @throws TokenDecryptException
     */
    public function decryptEmailToken(string $token): EmailTokenEntity
    {
        try {
            return new EmailTokenEntity(
                json_decode(
                    Crypt::decryptString($token),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (Exception $e) {
            throw new TokenDecryptException($e->getMessage());
        }
    }

    /**
     * @throws Exception
     * @throws TokenEncryptException
     */
    public function encryptEmailToken(BaseAuthenticatable $user): string
    {
        try {
            return Crypt::encryptString(
                json_encode(
                    [
                        'id' => $user->id,
                        'time' => CarbonImmutable::now()->timestamp,
                        'code' => (int)$user->getEmailVerificationCode(),
                        'guard' => $user::GUARD,
                    ],
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (Exception $e) {
            throw new TokenEncryptException($e->getMessage());
        }
    }
}


