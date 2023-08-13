<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UploadType extends \Rebing\GraphQL\Support\UploadType
{
    public static function type(): Type
    {
        return GraphQL::type('Upload');
    }
}
