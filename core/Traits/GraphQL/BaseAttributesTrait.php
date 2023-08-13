<?php

namespace Core\Traits\GraphQL;

trait BaseAttributesTrait
{
    public function attributes(): array
    {
        $description = static::DESCRIPTION;

        if (defined(static::class . '::PERMISSION') && !empty(static::PERMISSION)) {
            $permission = static::PERMISSION;

            if (is_array($permission)) {
                $permission = implode(', ', $permission);
            }

            $description .= PHP_EOL . 'Требуется разрешение: ' . $permission;
        }

        return [
            'name' => static::NAME,
            'description' => $description,
        ];
    }
}
