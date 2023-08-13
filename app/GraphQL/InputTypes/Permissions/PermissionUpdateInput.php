<?php

namespace App\GraphQL\InputTypes\Permissions;

use App\GraphQL\Types\BaseInputType;

class PermissionUpdateInput extends BaseInputType
{
    public const NAME = 'PermissionUpdateInputType';

    public function fields(): array
    {
        return [
            'translations' => [
                'type' => PermissionTranslationInput::nonNullList(),
            ],
        ];
    }
}
