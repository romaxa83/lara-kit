<?php

namespace Core\Services\Permissions;


use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use Core\Services\Permissions\Filters\PermissionFilter;
use Generator;
use Illuminate\Support\Collection;

class PermissionFilterService
{
    public function filter(User|Admin $user, Collection $permissions): Collection
    {
        if (
            !($user instanceof User)
            || !config('grants.filter_enabled')
        ) {
            return $permissions;
        }

        foreach ($this->getPermissionFilters() as $filter) {
            $permissions = $filter->filter($user, $permissions);

            if ($permissions->isEmpty()) {
                break;
            }
        }

        return $permissions;
    }

    /**
     * @return Generator|array<PermissionFilter>
     */
    protected function getPermissionFilters(): Generator|array
    {
        foreach (array_keys(config('grants.filters')) as $class) {
            yield app($class);
        }
    }
}
