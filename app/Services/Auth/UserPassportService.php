<?php

namespace App\Services\Auth;

use Core\Services\Auth\AuthPassportService;

class UserPassportService extends AuthPassportService
{

    public function getClientId(): int
    {
        return config('auth.oauth_client.users.id');
    }

    public function getClientSecret(): string
    {
        return config('auth.oauth_client.users.secret');
    }
}
