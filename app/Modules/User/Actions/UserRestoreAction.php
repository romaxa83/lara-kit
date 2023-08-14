<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Collections\UserEloquentCollection;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;

final class UserRestoreAction
{
    public function __construct(
        protected readonly UserRepository $repo
    )
    {}

    public function exec(
        User|UserEloquentCollection|array|int $value
    ): bool
    {
        if($value instanceof User){
            return $this->restoreOne($value);
        }
        if(is_numeric($value)){
            return $this->restoreOne(
                $this->repo->getBy('id', $value, withTrashed:true)
            );
        }
        if(is_array($value)) {
            return $this->restoreMany(
                $this->repo->getAllByFields(['id' => $value], withTrashed:true)
            );
        }

        return $this->restoreMany($value);
    }

    private function restoreOne(User $model): bool
    {
        return $this->restore($model);
    }

    private function restoreMany(UserEloquentCollection $collection): bool
    {
        $res = make_transaction(
            fn() => $collection->map(fn (User $m) => $this->restore($m))
        );

        return !$res->contains(false);
    }

    private function restore(User $model): bool
    {
        return $model->restore();
    }
}
