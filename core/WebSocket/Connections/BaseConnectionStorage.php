<?php

namespace Core\WebSocket\Connections;

use Core\Traits\Auth\AuthGuardsTrait;
use Core\WebSocket\Exceptions\SubscriptionAuthException;
use Core\WebSocket\Services\WsAuthService;
use Illuminate\Support\Arr;
use Ratchet\ConnectionInterface;

abstract class BaseConnectionStorage
{
    use AuthGuardsTrait;

    protected WsAuthService $authService;

    /** @var array<ConnectionEntity> */
    private array $connections = [];

    private array $subscriptionMap = [];

    public function __construct()
    {
        $this->setAuthService();
    }

    abstract protected function setAuthService(): void;

    /**
     * @param ConnectionInterface $conn
     * @param array $payload
     * @throws SubscriptionAuthException
     */
    public function addConnection(ConnectionInterface $conn, array $payload): void
    {
        $bearer = Arr::get($payload, 'Authorization');

        if (!$bearer) {
            throw new SubscriptionAuthException('Authorization token not provided');
        }

        if (!$user = $this->authService->getUserByBearer($bearer)) {
            throw new SubscriptionAuthException('Authorization token is invalid');
        }

        $connectionId = $this->getConnectionId($conn);

        $entity = new ConnectionEntity();
        $entity->setId($this->getConnectionId($conn));
        $entity->setConnection($conn);
        $entity->setUser($user);

        $this->connections[$connectionId] = $entity;
    }

    protected function getConnectionId(ConnectionInterface $conn): int
    {
        return spl_object_id($conn);
    }

    public function subscribe(ConnectionInterface $conn, array $payload, int|string $id): void
    {
        $connectionEntity = $this->connections[$this->getConnectionId($conn)] ?? null;

        if (!$connectionEntity) {
            return;
        }

        $connectionEntity->addSubscription(
            $subscription = SubscriptionEntity::makeFromPayload($payload, $id)
        );

        $this->subscriptionMap[$subscription->getSubscriptionName()][$connectionEntity->getId()] =
            $connectionEntity->getId();
    }

    /**
     * @param array $messagePayload
     * @return array<ConnectionEntity>
     */
    public function getConnectionsByMessagePayload(array $messagePayload): array
    {
        $subscriptionName = $messagePayload['name'];
        $userUniqId = Arr::get($messagePayload, 'userUniqId');

        $connections = [];

        foreach ($this->subscriptionMap[$subscriptionName] ?? [] as $connId) {
            $connection = $this->connections[$connId] ?? null;

            if (is_null($connection)) {
                continue;
            }

            $subscription = $connection->getSubscriptionByName($subscriptionName);

            if (
                $subscription &&
                (!$userUniqId || $connection->getUser()?->getUniqId() === $userUniqId)
            ) {
                $connections[] = compact('connection', 'subscription');
            }
        }

        return $connections;
    }

    public function removeConnection(ConnectionInterface $conn): void
    {
        $connId = $this->getConnectionId($conn);

        foreach ($this->subscriptionMap as $subscriptionName => $connectionMap) {
            unset($this->subscriptionMap[$subscriptionName][$connId]);
        }

        unset($this->connections[$this->getConnectionId($conn)]);
    }
}
