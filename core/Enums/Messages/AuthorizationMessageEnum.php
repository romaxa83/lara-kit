<?php

declare(strict_types=1);

namespace Core\Enums\Messages;

use Core\Enums\BaseEnum;

class AuthorizationMessageEnum extends BaseEnum
{
    public const UNAUTHORIZED = 'Unauthorized';
    public const AUTHORIZED = 'Authorized';
    public const NO_PERMISSION = 'No permission';
}
