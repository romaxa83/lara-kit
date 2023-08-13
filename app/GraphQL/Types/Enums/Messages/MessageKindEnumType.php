<?php

namespace App\GraphQL\Types\Enums\Messages;

use App\GraphQL\Types\GenericBaseEnumType;
use Core\Enums\Messages\MessageTypeEnum;

class MessageKindEnumType extends GenericBaseEnumType
{
    public const NAME = 'MessageTypeEnum';
    public const DESCRIPTION = 'Список типов сообщений.';
    public const ENUM_CLASS = MessageTypeEnum::class;
}
