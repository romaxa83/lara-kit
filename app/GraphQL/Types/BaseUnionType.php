<?php

namespace App\GraphQL\Types;

use Core\Traits\GraphQL\Types\BaseTypeTrait;
use Rebing\GraphQL\Support\UnionType;

abstract class BaseUnionType extends UnionType
{
    use BaseTypeTrait;
}
