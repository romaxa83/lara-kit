<?php

namespace App\Modules\Utils\Media\Enums;

use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use Core\Enums\BaseEnum;

class AvatarModelsEnum extends BaseEnum
{
    public const ADMIN = Admin::MORPH_NAME;
    public const USER = User::MORPH_NAME;
}

