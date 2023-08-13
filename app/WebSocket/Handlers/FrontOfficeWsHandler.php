<?php

namespace App\WebSocket\Handlers;

use App\WebSocket\Connections\FrontOfficeConnectionStorage;
use Core\WebSocket\Handlers\BaseGraphQLWsHandler;

class FrontOfficeWsHandler extends BaseGraphQLWsHandler
{
    protected function setSchema(): void
    {
        $this->schema = config('graphql.schemas.default');
    }

    protected function setConnectionStorage(): void
    {
        $this->connectionStorage = resolve(FrontOfficeConnectionStorage::class);
    }
}
