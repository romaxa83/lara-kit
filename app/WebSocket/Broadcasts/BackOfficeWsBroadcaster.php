<?php

namespace App\WebSocket\Broadcasts;

use Core\WebSocket\Broadcasts\BaseWsBroadcaster;

class BackOfficeWsBroadcaster extends BaseWsBroadcaster
{
    public const SUBSCRIPTION_ENDPOINT = 'graphql/BackOffice';

    protected function getSubscriptionEndpoint(): string
    {
        return self::SUBSCRIPTION_ENDPOINT;
    }
}
