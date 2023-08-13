<?php

namespace App\GraphQL\InputTypes;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;

abstract class BaseTranslationInputType extends BaseInputType
{
    public function fields(): array
    {
        return [
            'lang' => [
                'description' => 'Support: ' . support_langs_as_str(),
                'type' => NonNullType::string(),
            ],
            'title' => [
                'type' => NonNullType::string(),
            ],
        ];
    }
}
