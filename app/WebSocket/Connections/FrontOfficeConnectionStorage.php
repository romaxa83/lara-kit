<?php

namespace App\WebSocket\Connections;

use App\WebSocket\Services\FrontOfficeWsAuthService;
use Core\WebSocket\Connections\BaseConnectionStorage;

class FrontOfficeConnectionStorage extends BaseConnectionStorage
{
    protected function setAuthService(): void
    {
        $this->authService = resolve(FrontOfficeWsAuthService::class);
    }
}
