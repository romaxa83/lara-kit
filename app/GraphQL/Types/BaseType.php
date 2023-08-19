<?php

namespace App\GraphQL\Types;

use App\Models\ListPermission;
use Core\GraphQL\Fields\PermissionField;
use Core\Traits\GraphQL\Types\BaseTypeTrait;

abstract class BaseType extends \Core\GraphQL\Types\BaseType
{
    use BaseTypeTrait;

    public function fields(): array
    {
        $fields = parent::fields();

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

