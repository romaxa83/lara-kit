<?php

namespace Core\WebSocket\Traits;

use Core\WebSocket\Broadcasts\BaseWsBroadcaster;

trait InteractsWithConnection
{
    protected function isHandShake(array $data): bool
    {
        return $this->is($data, self::CONNECTION_INIT);
    }

    private function is(array $data, string $type): bool
    {
        return isset($data['type']) && $data['type'] === $type;
    }

    protected function isSubscription(array $data): bool
    {
        return $this->is($data, self::GRAPH_QL_SUBSCRIBE);
    }

    protected function isNotification(array $data): bool
    {
        return $this->is($data, BaseWsBroadcaster::NOTIFICATION_TYPE);
    }

    protected function isClose(array $data): bool
    {
        return $this->is($data, self::GRAPH_QL_UNSUBSCRIBE);
    }
}
