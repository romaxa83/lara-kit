<?php

namespace App\Providers;

use App\Models\Passport\AuthCode;
use App\Models\Passport\Client;
use App\Models\Passport\PersonalAccessClient;
use App\Models\Passport\Token;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class PassportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Passport::useTokenModel(Token::class);
        Passport::useClientModel(Client::class);
        Passport::useAuthCodeModel(AuthCode::class);
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);

        Passport::tokensExpireIn(
            now()->addMinutes(config('auth.oauth_tokens_expire_in'))
        );
        Passport::refreshTokensExpireIn(
            now()->addMinutes(config('auth.oauth_refresh_tokens_expire_in'))
        );
        Passport::personalAccessTokensExpireIn(
            now()->addMinutes(config('auth.oauth_personal_access_tokens_expire_in'))
        );
    }
}
