<?php

namespace App\GraphQL\Subscriptions\BackOffice;

use App\WebSocket\Broadcasts\BackOfficeWsBroadcaster;
use Core\WebSocket\Broadcasts\BaseWsBroadcaster;
use Core\WebSocket\GraphQL\Subscriptions\BaseSubscription;

abstract class BaseBackOfficeSubscription extends BaseSubscription
{
    public function __construct()
    {
        $this->setAdminGuard();
    }

    protected static function broadcaster(): BaseWsBroadcaster
    {
        return resolve(BackOfficeWsBroadcaster::class);
    }
}
