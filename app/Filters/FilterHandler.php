<?php

namespace App\Filters;

use EloquentFilter\ModelFilter;

interface FilterHandler
{
    public static function check(string $query): bool;

    public function addConditions(ModelFilter $filter, string $query): void;
}
