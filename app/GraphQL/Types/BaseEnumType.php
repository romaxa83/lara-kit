<?php

namespace App\GraphQL\Types;

use Core\Traits\GraphQL\Types\BaseTypeTrait;
use GraphQL\Type\Definition\NullableType;
use Rebing\GraphQL\Support\EnumType;

abstract class BaseEnumType extends EnumType implements NullableType
{
    use BaseTypeTrait;
}
