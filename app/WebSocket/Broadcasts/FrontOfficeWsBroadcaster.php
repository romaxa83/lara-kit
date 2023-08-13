<?php

namespace App\WebSocket\Broadcasts;

use Core\WebSocket\Broadcasts\BaseWsBroadcaster;

class FrontOfficeWsBroadcaster extends BaseWsBroadcaster
{
    public const SUBSCRIPTION_ENDPOINT = 'graphql/FrontOffice';

    protected function getSubscriptionEndpoint(): string
    {
        return self::SUBSCRIPTION_ENDPOINT;
    }
}
