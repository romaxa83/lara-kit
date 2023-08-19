<?php

namespace Core\GraphQL\Types\Media;

use App\GraphQL\Types\BaseType;
use GraphQL\Type\Definition\Type;

class MediaConversionType extends BaseType
{
    public const NAME = 'MediaConventionType';

    public function fields(): array
    {
        return [
            'convention' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }
}

