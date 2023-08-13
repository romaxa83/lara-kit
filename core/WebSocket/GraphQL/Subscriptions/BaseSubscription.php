<?php

namespace Core\WebSocket\GraphQL\Subscriptions;

use Closure;
use Core\Traits\Auth\AuthGuardsTrait;
use Core\Traits\GraphQL\BaseAttributesTrait;
use Core\Traits\GraphQL\ThrowableResolverTrait;
use Core\WebSocket\Broadcasts\BaseWsBroadcaster;
use Core\WebSocket\Broadcasts\BroadcastSubscription;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

abstract class BaseSubscription extends Query
{
    use AuthGuardsTrait;
    use BaseAttributesTrait;
    use ThrowableResolverTrait;

    public const NAME = '';
    public const DESCRIPTION = '';
    public const PERMISSION = '';

    public static function notify(): BroadcastSubscription
    {
        return BroadcastSubscription::broadcastWith(
            static::broadcaster(),
            static::NAME
        );
    }

    abstract protected static function broadcaster(): BaseWsBroadcaster;

    public function authorize(
        mixed $root,
        array $args,
        mixed $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool {
        return empty(static::PERMISSION) || $this->can(static::PERMISSION);
    }

    abstract public function type(): Type;

    public function attributes(): array
    {
        return [
            'name' => static::NAME,
        ];
    }
}
