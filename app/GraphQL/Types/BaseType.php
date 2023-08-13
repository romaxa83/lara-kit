<?php

namespace App\GraphQL\Types;

use App\Models\ListPermission;
use Core\GraphQL\Fields\PermissionField;
use Core\Traits\GraphQL\Types\BaseTypeTrait;
use GraphQL\Type\Definition\NullableType;
use Rebing\GraphQL\Support\Type;

abstract class BaseType extends Type implements NullableType
{
    use BaseTypeTrait;

    public function fields(): array
    {
        $fields = [
            'id' => [
                'type' => NonNullType::id(),
            ],
            'created_at' => [
                'type' => NonNullType::string(),
            ],
            'updated_at' => [
                'type' => NonNullType::string(),
            ],
        ];

        if (
            ($attributes = $this->getAttributes())
            && isset($attributes['model'])
            && in_array(ListPermission::class, class_implements($attributes['model']), true)
        ) {
            $fields['permission'] = new PermissionField();
        }

        return $fields;
    }
}
