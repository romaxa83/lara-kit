<?php

namespace Core\WebSocket\Traits;

use JsonException;
use Ratchet\RFC6455\Messaging\MessageInterface;

trait InteractsWithMessage
{
    /**
     * @param  MessageInterface  $msg
     * @return array
     * @throws JsonException
     */
    protected function getMessagePayload(MessageInterface $msg): array
    {
        return json_decode($msg->getPayload(), true, 512, JSON_THROW_ON_ERROR);
    }
}
