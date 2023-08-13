<?php

namespace App\GraphQL\Types\Messages;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

/**
 * @see ResponseMessageEntity
 */
class ResponseMessageType extends BaseType
{
    public const NAME = 'ResponseMessageType';
    public const DESCRIPTION = 'Сообщение для отображения на фронте.';

    public function fields(): array
    {
        return [
            'message' => [
                'type' => NonNullType::string(),
            ],
            'success' => [
                'type' => Type::boolean(),
            ],
        ];
    }
}
