<?php

namespace App\GraphQL\Types;

use Laravel\Passport\Passport;

abstract class BaseAuthTokenType extends BaseType
{
    public function fields(): array
    {
        return [
            'token_type' => [
                'type' => NonNullType::string(),
            ],
            /** @see BaseLoginType::resolveAccessExpiresInField() */
            'access_expires_in' => [
                'type' => NonNullType::int(),
            ],
            /** @see BaseLoginType::resolveRefreshExpiresInField() */
            'refresh_expires_in' => [
                'type' => NonNullType::int(),
            ],
            'access_token' => [
                'type' => NonNullType::string(),
            ],
            'refresh_token' => [
                'type' => NonNullType::string(),
            ],
        ];
    }

    protected function resolveAccessExpiresInField(array $root): int
    {
        return $root['expires_in'];
    }

    protected function resolveRefreshExpiresInField(): int
    {
        return date_interval_to_seconds(
            Passport::$refreshTokensExpireIn
        );
    }
}

