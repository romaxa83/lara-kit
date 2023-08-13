<?php

declare(strict_types=1);

namespace Core\Enums;

use Core\GraphQL\Types\Enums\GraphQLTypeEnum;
use Core\Traits\GraphQL\Types\GraphQLEnumGenericTrait;

abstract class BaseGraphQLEnum extends BaseEnum implements GraphQLTypeEnum
{
    use GraphQLEnumGenericTrait;
}
