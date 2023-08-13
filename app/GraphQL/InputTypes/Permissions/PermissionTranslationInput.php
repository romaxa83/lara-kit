<?php

namespace App\GraphQL\InputTypes\Permissions;

use App\GraphQL\InputTypes\BaseTranslationInputType;

class PermissionTranslationInput extends BaseTranslationInputType
{
    public const NAME = 'PermissionTranslationInput';

    public function fields(): array
    {
        return parent::fields();
    }
}
