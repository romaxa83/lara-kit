<?php

namespace Core\WebSocket\Jobs;

use Core\WebSocket\Broadcasts\BaseWsBroadcaster;
use Core\WebSocket\Contracts\Subscribable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JsonException;
use WebSocket\BadOpcodeException;

class WsBroadcastJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const QUEUE = 'ws_broadcast_job';

    protected string $subscription;
    protected ?Subscribable $user = null;
    protected array $context = [];

    public function __construct(
        protected BaseWsBroadcaster $broadcaster
    ) {
        $this->onQueue(static::QUEUE);
    }

    public function toSubscription(string $subscriptionName): self
    {
        $this->subscription = $subscriptionName;

        return $this;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setUser(?Subscribable $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @throws BadOpcodeException
     * @throws JsonException
     */
    public function handle(): void
    {
        $this->broadcaster->setUser($this->user);
        $this->broadcaster->notify($this->subscription, $this->context);
    }
}
