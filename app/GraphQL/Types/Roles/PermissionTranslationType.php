<?php

namespace App\GraphQL\Types\Roles;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Permissions\Models\PermissionTranslation;

class PermissionTranslationType extends BaseType
{
    public const NAME = 'PermissionTranslationType';
    public const MODEL = PermissionTranslation::class;

    public function fields(): array
    {
        return [
            'id' => [
                'type' => NonNullType::id(),
            ],
            'title' => [
                'type' => NonNullType::string(),
            ],
            'language' => [
                'alias' => 'lang',
                'type' => NonNullType::string(),
            ],
        ];
    }
}
