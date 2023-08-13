<?php

namespace Core\Services\Permissions\Filters;

use App\Modules\Permissions\Models\Permission;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

interface PermissionFilter
{
    /**
     * @param User $user
     * @param Collection<Permission> $permissions
     * @return Collection<Permission>
     */
    public function filter(User $user, Collection $permissions): Collection;
}
