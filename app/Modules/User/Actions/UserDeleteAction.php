<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Collections\UserEloquentCollection;
use App\Modules\User\Models\User;

final class UserDeleteAction
{
    public function exec(User|UserEloquentCollection $model, bool $force = false): bool
    {
        return $model instanceof UserEloquentCollection
            ? $this->removeMany($model, $force)
            : $this->removeOne($model, $force);
    }

    private function removeOne(User $model, bool $force = false): bool
    {
        return $force ? $this->forceDelete($model) : $model->delete();
    }

    private function removeMany(UserEloquentCollection $collection, bool $force = false): bool
    {
        $res = make_transaction(
            fn() => $collection->map(fn(User $m): bool => $force ? $this->forceDelete($m) : $m->delete())
        );

        return !$res->contains(false);
    }

    public function forceDelete(User $model): bool
    {
        $model->phones()->delete();

        return $model->forceDelete();
    }
}
