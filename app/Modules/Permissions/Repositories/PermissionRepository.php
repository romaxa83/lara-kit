<?php

namespace App\Modules\Permissions\Repositories;

use App\Modules\Permissions\Models\Permission;
use Core\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

final class PermissionRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Permission::class;
    }

    public function getPermissionsIdByKey(array $keys)
    {
        return $this->queryBuilder()
            ->select(['id'])
            ->whereIn('name', $keys)
            ->get()
            ->pluck('id')
            ->toArray()
            ;
    }
}
