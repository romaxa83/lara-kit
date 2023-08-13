<?php

declare(strict_types=1);

namespace Core\Services\Permissions;

use App\Modules\User\Models\User;

class PermissionTreeStorage
{
    public function getAllMainRelated(array $permissions, string $guard = User::GUARD): array
    {
        $result = [];

        $reversedTree = $this->getReversed($guard);

        /**
         * Массив $permissions всегда будет иметь 1-2 значения, посему проблем с производительностью быть не должно
         */
        foreach ($permissions as $permission) {
            $result = array_merge($result, $reversedTree[$permission] ?? []);
        }

        return $result;
    }

    protected function getReversed(string $guard): array
    {
        $result = [];

        foreach (config('grants.relations.' . $guard) ?? [] as $mainPermissionClass => $relatedPermissionClasses) {
            foreach ($relatedPermissionClasses as $class) {
                $key = $this->normalizePermission($class);

                $result[$key][] = $this->normalizePermission($mainPermissionClass);
            }
        }

        return $result;
    }

    public function normalizePermission(string $permissionClass): string
    {
        return $permissionClass::KEY;
    }

    public function getMainKeys(string $key, string $guard = User::GUARD): array
    {
        $reversedTree = $this->getReversed($guard);

        return $reversedTree[$key] ?? [];
    }
}
