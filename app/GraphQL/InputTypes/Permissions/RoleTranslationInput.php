<?php

namespace App\GraphQL\InputTypes\Permissions;

use App\GraphQL\InputTypes\BaseTranslationInputType;

class RoleTranslationInput extends BaseTranslationInputType
{
    public const NAME = 'RoleTranslationInput';

    public function fields(): array
    {
        return parent::fields();
    }
}

