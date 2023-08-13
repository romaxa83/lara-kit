<?php

namespace Core\Permissions;

abstract class BasePermission implements Permission
{
    public function getKey(): string
    {
        return static::KEY;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'position' => $this->getPosition(),
        ];
    }
}
