<?php

namespace App\Helpers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class DbConnections
{
    public const DEFAULT = 'mysql';

    public static function default(): Connection
    {
        return static::getConnection(self::DEFAULT);
    }

    public static function getConnection(string $connection): Connection
    {
        return DB::connection($connection);
    }
}
