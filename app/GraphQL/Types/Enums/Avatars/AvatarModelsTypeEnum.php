<?php

namespace App\GraphQL\Types\Enums\Avatars;

use App\GraphQL\Types\GenericBaseEnumType;
use App\Modules\Utils\Media\Enums\AvatarModelsEnum;

class AvatarModelsTypeEnum extends GenericBaseEnumType
{
    public const NAME = 'AvatarModelsTypeEnum';
    public const DESCRIPTION = 'Список сущностей, которые поддерживают загрузку аватаров';
    public const ENUM_CLASS = AvatarModelsEnum::class;
}
