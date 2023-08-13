<?php

namespace App\GraphQL\Subscriptions\FrontOffice;

use App\WebSocket\Broadcasts\FrontOfficeWsBroadcaster;
use Core\WebSocket\Broadcasts\BaseWsBroadcaster;
use Core\WebSocket\GraphQL\Subscriptions\BaseSubscription;

abstract class BaseFrontOfficeSubscription extends BaseSubscription
{
    protected static function broadcaster(): BaseWsBroadcaster
    {
        return resolve(FrontOfficeWsBroadcaster::class);
    }
}
