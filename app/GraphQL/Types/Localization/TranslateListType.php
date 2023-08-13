<?php

namespace App\GraphQL\Types\Localization;

use App\GraphQL\Types\BaseType;

class TranslateListType extends BaseType
{
    public const NAME = 'TranslateListType';

    private string $type;

    protected $attributes = [
        'name' => self::NAME,
    ];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function fields(): array
    {
        $fields = [];
        foreach (languages() as $language) {
            $fields[$language->slug] = $this->type;
        }
        return $fields;
    }
}
