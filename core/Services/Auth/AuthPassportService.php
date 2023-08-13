<?php

namespace Core\Services\Auth;

use App\Models\Passport\Token;
use Core\Models\BaseAuthenticatable;
use Laravel\Passport\RefreshToken;
use Throwable;

abstract class AuthPassportService
{
    public function __construct(
        protected PassportService $passportService
    ) {
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     * @throws Throwable
     */
    public function auth(string $username, string $password): array
    {
        return $this->passportService->auth(
            $username,
            $password,
            $this->getClientId(),
            $this->getClientSecret()
        );
    }

    abstract public function getClientId(): int;

    abstract public function getClientSecret(): string;

    /**
     * @param string $refreshToken
     * @return array
     * @throws Throwable
     */
    public function refreshToken(string $refreshToken): array
    {
        return $this->passportService->refreshToken(
            $refreshToken,
            $this->getClientId(),
            $this->getClientSecret()
        );
    }

    public function logout(BaseAuthenticatable $authenticatable): bool
    {
        try {
            /** @var Token $token */
            $token = $authenticatable->token();

            RefreshToken::query()
                ->where('access_token_id', $token->id)
                ->delete();

            $token->clearInCache();

            return $token->delete();
        } catch (Throwable) {
            return false;
        }
    }
}
