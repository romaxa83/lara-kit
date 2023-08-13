<?php

namespace App\Modules\Utils\Tokenizer;

use App\Modules\Utils\Tokenizer\Entities\TokenEntity;
use App\Modules\Utils\Tokenizer\Exceptions\TokenDecryptException;
use App\Modules\Utils\Tokenizer\Exceptions\TokenEncryptException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Crypt;

class Tokenizer
{
    /**
     * @throws TokenDecryptException
     */
    public static function decryptToken(string $token): TokenEntity
    {
        try {
            return new TokenEntity(
                json_decode(
                    Crypt::decryptString($token),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (\Exception $e) {
            throw new TokenDecryptException($e->getMessage());
        }
    }

    /**
     * @throws TokenEncryptException
     */
    public static function encryptToken(array $payload): string
    {
        try {
            return Crypt::encryptString(
                json_encode(
                    [
                        'model_id' => $payload['model_id'],
                        'model_class' => $payload['model_class'],
                        'time_at' => CarbonImmutable::now()->timestamp,
                        'field_check_code' => $payload['field_check_code'],
                        'code' => $payload['code'],
                    ],
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (\Exception $e) {
            throw new TokenEncryptException($e->getMessage());
        }
    }
}

