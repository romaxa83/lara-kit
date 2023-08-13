<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AddSelectTrait
{
    public static function bootAddSelectTrait(): void
    {
        if (static::$eagerLoadingFields) {
            static::addGlobalScope(
                'addMissingFieldsForEagerLoading',
                function (Builder $builder) {
                    if (!$builder->getQuery()->columns) {
                        return;
                    }

                    $columns = self::prepareColumnsToAddToSelect($builder);
                    foreach ($builder->getEagerLoads() as $relation => $callback) {
                        if (array_key_exists($relation, $columns)) {
                            $builder->addSelect($columns[$relation]);
                        }
                    }
                }
            );
        }
    }

    protected static function prepareColumnsToAddToSelect(Builder $builder): array
    {
        return array_map(
            function (string $column) use ($builder) {
                return $builder->getQuery()->from . '.' . $column;
            },
            static::$eagerLoadingFields
        );
    }
}
