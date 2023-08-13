<?php

namespace Core\Permissions;

abstract class BasePermissionGroup implements PermissionGroup
{
    public function __construct(protected array $permissions)
    {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'position' => $this->getPosition(),
            'permissions' => collect($this->getPermissions())->toArray(),
        ];
    }

    public function getKey(): string
    {
        return static::KEY;
    }

    public function getPosition(): int
    {
        return 0;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
