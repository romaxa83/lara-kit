<?php

declare(strict_types=1);

namespace Core\Dto;

use Core\GraphQL\Types\Inputs\GraphQLTypeInput;
use Core\Traits\GraphQL\Types\GraphQLInputGenericTrait;

abstract class BaseGraphQLDto implements GraphQLTypeInput
{
    use GraphQLInputGenericTrait;
}
