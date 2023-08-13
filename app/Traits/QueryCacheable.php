<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static self|Builder cacheFor(int|Carbon $duration)
 * @method static self|Builder cacheTags(array $tags)
 * @method static bool flushQueryCache(array $tags)
 */
trait QueryCacheable
{
    use \Rennokki\QueryCache\Traits\QueryCacheable;
}
