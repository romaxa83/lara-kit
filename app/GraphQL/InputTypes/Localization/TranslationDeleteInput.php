<?php

namespace App\GraphQL\InputTypes\Localization;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;

class TranslationDeleteInput extends BaseInputType
{
    public const NAME = 'TranslationDeleteInputType';

    public function fields(): array
    {
        return [
            'place' => [
                'type' => NonNullType::string(),
            ],
            'key' => [
                'type' => NonNullType::string(),
            ],
            'lang' => [
                'type' => NonNullType::string(),
            ],
        ];
    }
}
