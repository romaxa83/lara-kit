<?php

namespace Core\WebSocket\Contracts;

interface Subscribable
{
    public function getKey();

    public function getUniqId(): string;
}
