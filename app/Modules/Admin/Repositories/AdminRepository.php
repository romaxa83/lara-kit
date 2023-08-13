<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Enums\BaseRole;
use Core\Repositories\BaseRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

final class AdminRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Admin::class;
    }

    public function getByRoleName(
        string $role,
        array $select = ['id']
    ): Collection
    {
        return Admin::query()
            ->whereHas('roles',
                fn(Builder $b): Builder => $b->where('name', $role)
            )->get();
    }

    public function getSuperAdmin(array $select = ['*'], bool $asObj = false): null|Admin|stdClass
    {
        return Admin::query()
            ->select($select)
            ->whereHas('roles',
                fn(Builder $b): Builder => $b->where('name', BaseRole::SUPER_ADMIN)
            )
            ->when($asObj,
                fn(Builder $b) => $b->toBase()
            )
            ->first();
    }
}

