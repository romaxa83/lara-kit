<?php

namespace Core\WebSocket\Broadcasts;

use Core\WebSocket\Contracts\Subscribable;
use Core\WebSocket\Jobs\WsBroadcastJob;

class BroadcastSubscription
{
    private ?Subscribable $user = null;
    private array $context = [];

    protected function __construct(
        private BaseWsBroadcaster $broadcaster,
        private string $subscriptionName,
    ) {
    }

    public static function broadcastWith(BaseWsBroadcaster $broadcaster, string $subscription): self
    {
        return new static($broadcaster, $subscription);
    }

    public function toUser(Subscribable $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function withContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function broadcast(): void
    {
        dispatch(
            (new WsBroadcastJob($this->broadcaster))
                ->toSubscription($this->subscriptionName)
                ->setContext($this->context)
                ->setUser($this->user)
        );
    }
}
