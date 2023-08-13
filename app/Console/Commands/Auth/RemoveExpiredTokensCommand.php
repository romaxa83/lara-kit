<?php


namespace App\Console\Commands\Auth;

use App\Models\Passport\Token;
use Illuminate\Console\Command;
use Laravel\Passport\RefreshToken;

class RemoveExpiredTokensCommand extends Command
{
    protected $signature = 'auth:remove-expired-tokens';

    protected $description = 'Remove all expired bearer tokens';

    public function handle(): int
    {
        RefreshToken::query()
            ->join('oauth_access_tokens', 'oauth_access_tokens.id', '=', 'oauth_refresh_tokens.access_token_id')
            ->where('oauth_refresh_tokens.expires_at', '<=', now())
            ->where('oauth_access_tokens.revoked', false)
            ->delete();

        Token::query()
            ->where('expires_at', '<=', now())
            ->where('revoked', false)
            ->delete();

        return self::SUCCESS;
    }
}

