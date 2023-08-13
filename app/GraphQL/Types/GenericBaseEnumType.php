<?php

namespace App\GraphQL\Types;

use Core\Enums\BaseEnum;

abstract class GenericBaseEnumType extends BaseEnumType
{
    public function attributes(): array
    {
        $attributes = [];

        if (defined(static::class . '::ENUM_CLASS')) {
            /** @var \Core\Enums\BaseEnum $class */
            $class = static::ENUM_CLASS;

            $attributes = [
                'values' => collect($class::getValues())
                    ->mapWithKeys(static fn(string $type) => [$type => $type])
                    ->toArray()
            ];
        }

        return array_merge(
            parent::attributes(),
            $attributes
        );
    }
}
