<?php

namespace App\Models\Passport;

use App\Traits\QueryCacheable;

/**
 * @property int id
 * @property string secret
 */
class Client extends \Laravel\Passport\Client
{
    use QueryCacheable;

    public int $cacheFor = 300;

    public array $cacheTags = ['passport_client'];

    public string $cachePrefix = 'passport_client_';

    public function __construct(array $attributes = [])
    {
        $this->cacheFor = config('passport.cache.oauth_clients.duration');

        parent::__construct($attributes);
    }
}
