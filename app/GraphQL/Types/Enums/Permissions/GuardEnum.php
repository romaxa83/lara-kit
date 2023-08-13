<?php

namespace App\GraphQL\Types\Enums\Permissions;

use App\GraphQL\Types\GenericBaseEnumType;
use App\Modules\Permissions\Enums\Guard;
use Core\GraphQL\Types\Enums\GraphQLTypeCastedEnum;
use GraphQL\Type\Definition\Type as GraphqlType;

class GuardEnum extends GenericBaseEnumType
{
    public const NAME = 'GuardEnumType';
    public const ENUM_CLASS = Guard::class;

    public function toType(): GraphqlType
    {
        return new GraphQLTypeCastedEnum($this->toArray());
    }
}

