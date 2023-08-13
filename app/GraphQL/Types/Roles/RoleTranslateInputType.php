<?php

namespace App\GraphQL\Types\Roles;

use App\GraphQL\Types\BaseInputTranslateType;
use App\GraphQL\Types\NonNullType;

class RoleTranslateInputType extends BaseInputTranslateType
{
    public const NAME = 'RoleTranslateInputType';

    public function fields(): array
    {
        return [
            'title' => [
                'description' => 'Название',
                'type' => NonNullType::string(),
                'rules' => ['max:250']
            ],
            'language' => [
                'description' => 'Language',
                'type' => NonNullType::string(),
                'rules' => ['max:3']
            ],
        ];
    }


}
