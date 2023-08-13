<?php

namespace App\GraphQL\ScalarTypes;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class EmailType extends ScalarType
{

    public function serialize($value)
    {
        return (string)$value;
    }

    public function parseValue($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Error(__('types.email') . ': ' . Utils::printSafeJson($value));
        }

        return $value;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new Error(__('exceptions.query_error') . ': ' . $valueNode->kind, [$valueNode]);
        }

        if (!filter_var($valueNode->value, FILTER_VALIDATE_EMAIL)) {
            throw new Error(__('types.email'));
        }

        return $valueNode->value;
    }
}
