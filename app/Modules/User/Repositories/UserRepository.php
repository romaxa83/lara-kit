<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Core\Repositories\BaseRepository;

final class UserRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return User::class;
    }
}
