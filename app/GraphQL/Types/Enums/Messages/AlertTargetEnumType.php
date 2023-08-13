<?php

namespace App\GraphQL\Types\Enums\Messages;

use App\GraphQL\Types\GenericBaseEnumType;
use Core\Enums\Messages\MessageTargetEnum;

class AlertTargetEnumType extends GenericBaseEnumType
{
    public const NAME = 'AlertEnumType';
    public const DESCRIPTION = 'Список возможных привязок предупреждающих сообщений.';
    public const ENUM_CLASS = MessageTargetEnum::class;
}
