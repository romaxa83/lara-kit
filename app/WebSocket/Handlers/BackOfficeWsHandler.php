<?php

namespace App\WebSocket\Handlers;

use App\WebSocket\Connections\BackOfficeConnectionStorage;
use Core\WebSocket\Handlers\BaseGraphQLWsHandler;

class BackOfficeWsHandler extends BaseGraphQLWsHandler
{
    protected function setSchema(): void
    {
        $this->schema = config('graphql.schemas.BackOffice');
    }

    protected function setConnectionStorage(): void
    {
        $this->connectionStorage = resolve(BackOfficeConnectionStorage::class);
    }
}
