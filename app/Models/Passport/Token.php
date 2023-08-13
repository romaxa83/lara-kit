<?php

namespace App\Models\Passport;

use App\Traits\QueryCacheable;
use Carbon\Carbon;
use Rennokki\QueryCache\Query\Builder;

/**
 * @property int id
 * @property int user_id
 * @property int client_id
 * @property string|null name
 * @property string scopes
 * @property bool revoked
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon expires_at
 */
class Token extends \Laravel\Passport\Token
{
    use QueryCacheable;

    public const TABLE = 'oauth_access_tokens';

    public int $cacheFor = 300;

    public string $cachePrefix = 'passport_token_';

    protected $table = self::TABLE;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->cacheFor = config('passport.cache.oauth_access_tokens.duration');
    }

    public function clearInCache(): bool
    {
        /** @var Builder $builder */
        $builder = $this->newModelQuery()
            ->where('id', $this->getKey())
            ->limit(1)
            ->getQuery();

        return $builder->getCache()->forget(
            $builder->getCacheKey()
        );
    }
}
