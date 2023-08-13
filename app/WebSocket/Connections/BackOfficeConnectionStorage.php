<?php

namespace App\WebSocket\Connections;

use App\WebSocket\Services\BackOfficeWsAuthService;
use Core\WebSocket\Connections\BaseConnectionStorage;

class BackOfficeConnectionStorage extends BaseConnectionStorage
{
    protected function setAuthService(): void
    {
        $this->authService = resolve(BackOfficeWsAuthService::class);
    }
}
