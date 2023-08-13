<?php

namespace App\Services\Auth;

use Core\Services\Auth\AuthPassportService;

class AdminPassportService extends AuthPassportService
{
    public function getClientId(): int
    {
        return config('auth.oauth_client.admins.id');
    }

    public function getClientSecret(): string
    {
        return config('auth.oauth_client.admins.secret');
    }
}
