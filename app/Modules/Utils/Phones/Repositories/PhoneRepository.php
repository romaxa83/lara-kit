<?php

namespace App\Modules\Utils\Phones\Repositories;

use App\Modules\Utils\Phones\Models\Phone;
use Core\Repositories\BaseRepository;

final class PhoneRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Phone::class;
    }
}
