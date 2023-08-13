<?php

namespace App\Modules\Admin\Actions;

use App\Modules\Admin\Collections\AdminEloquentCollection;
use App\Modules\Admin\Models\Admin;

final class AdminDeleteAction
{
    public function exec(Admin|AdminEloquentCollection $model, bool $force = false): bool
    {
        return $model instanceof AdminEloquentCollection
            ? $this->removeMany($model, $force)
            : $this->removeOne($model, $force);
    }

    private function removeOne(Admin $model, bool $force = false): bool
    {
        return $force ? $this->forceDelete($model) : $model->delete();
    }

    private function removeMany(AdminEloquentCollection $collection, bool $force = false): bool
    {
        $res = make_transaction(
            fn() => $collection->map(fn(Admin $m): bool => $force ? $this->forceDelete($m) : $m->delete())
        );

        return !$res->contains(false);
    }

    public function forceDelete(Admin $model): bool
    {
        $model->phones()->delete();

        return $model->forceDelete();
    }
}
