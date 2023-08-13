<?php

namespace App\Modules\Permissions\Repositories;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Models\Role;
use Core\Repositories\BaseRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class RoleRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Role::class;
    }

    public function getSuperAdmin(
        array $select = ['*'],
        bool $asObj = false
    ): null|Role|\stdClass
    {
        return $this->eloquentBuilder()
            ->select($select)
            ->where('name', BaseRole::SUPER_ADMIN)
            ->when($asObj,
                fn(Builder $b) => $b->toBase()
            )
            ->first();
    }
}
