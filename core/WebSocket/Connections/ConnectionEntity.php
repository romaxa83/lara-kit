<?php

namespace Core\WebSocket\Connections;

use Core\WebSocket\Contracts\Subscribable;
use Ratchet\ConnectionInterface;

class ConnectionEntity
{
    private int $id;
    private ConnectionInterface $connection;
    private ?Subscribable $user = null;

    /** @var array<SubscriptionEntity> */
    private array $subscriptions = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function setConnection(ConnectionInterface $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getUser(): ?Subscribable
    {
        return $this->user;
    }

    public function setUser(?Subscribable $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function addSubscription(SubscriptionEntity $subscription): self
    {
        $this->subscriptions[$subscription->getSubscriptionName()] = $subscription;

        return $this;
    }

    public function getSubscriptionByName(string $subscriptionName): ?SubscriptionEntity
    {
        return $this->subscriptions[$subscriptionName] ?? null;
    }
}
