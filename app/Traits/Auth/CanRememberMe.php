<?php

namespace App\Traits\Auth;

use GraphQL\Type\Definition\Type;
use Laravel\Passport\Passport;

trait CanRememberMe
{
    protected function rememberMeArg(): array
    {
        return [
            'remember_me' => [
                'type' => Type::boolean(),
                'defaultValue' => false,
            ]
        ];
    }

    protected function rememberMeRule(string $prefix = ''): array
    {
        return [
            $prefix.'remember_me' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    protected function setRefreshTokenTtl(bool $rememberMe): void
    {
        if ($rememberMe) {
            Passport::refreshTokensExpireIn(
                now()->addMinutes(config('auth.oauth_remembered_refresh_tokens_expire_in'))
            );

            return;
        }

        Passport::refreshTokensExpireIn(
            now()->addMinutes(config('auth.oauth_refresh_tokens_expire_in'))
        );
    }
}
