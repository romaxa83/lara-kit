<?php

namespace App\GraphQL\InputTypes\Localization;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;

class TranslationInput extends BaseInputType
{
    public const NAME = 'TranslationInputType';

    public function fields(): array
    {
        return [
            'place' => [
                'type' => NonNullType::string(),
            ],
            'key' => [
                'type' => NonNullType::string(),
            ],
            'text' => [
                'type' => NonNullType::string(),
            ],
            'lang' => [
                'type' => NonNullType::string(),
            ],
        ];
    }
}
