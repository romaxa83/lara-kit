<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;

abstract class BaseInputTranslateType extends BaseInputType
{
    public function fields(): array
    {
        return [
            'id' => [
                'description' => 'ID',
                'type' => Type::id(),
            ],
            'name' => [
                'description' => 'Название',
                'type' => NonNullType::string(),
                'rules' => ['max:250']
            ],
            'language' => [
                'description' => 'Language',
                'type' => NonNullType::string(),
                'rules' => ['max:3']
            ],
            'slug' => [
                'description' => 'Уникальный слаг',
                'type' => Type::string(),
                'rules' => ['max:250']
            ],
            'content' => [
                'description' => 'Описание',
                'type' => Type::string(),
                'rules' => ['max:80000']
            ],
            'h1' => [
                'description' => 'h1',
                'type' => Type::string(),
                'rules' => ['max:250']
            ],
            'title' => [
                'description' => 'title',
                'type' => Type::string(),
                'rules' => ['max:250']
            ],
            'description' => [
                'description' => 'description',
                'type' => Type::string(),
                'rules' => ['max:80000']
            ],
            'keywords' => [
                'description' => 'keywords',
                'type' => Type::string(),
                'rules' => ['max:80000']
            ]
        ];
    }
}
