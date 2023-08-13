<?php

namespace App\GraphQL\Types\Messages;

use App\GraphQL\Types\Enums\Messages\AlertTargetEnumType;
use Core\Enums\Messages\MessageTargetEnum;

class AlertMessageType extends ResponseMessageType
{
    public const NAME = 'AlertMessageType';

    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                'target' => [
                    'type' => AlertTargetEnumType::type(),
                    'description' => 'Возможные варианты: ' . MessageTargetEnum::listToString(),
                ],
            ]
        );
    }
}
