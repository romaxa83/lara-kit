<?php

namespace Core\GraphQL\Fields;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Field;

abstract class BasePermissionField extends Field
{
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    protected $attributes = [
        'selectable' => false,
        'description' => 'Список возможных разрешений для текущей модели.',
    ];

    public function type(): Type
    {
        return Type::listOf(
            Type::string()
        );
    }

    abstract protected function resolve($root, array $args): array;
}
