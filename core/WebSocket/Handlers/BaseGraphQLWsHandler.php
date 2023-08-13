<?php

namespace Core\WebSocket\Handlers;

use Core\Models\BaseAuthenticatable;
use Core\WebSocket\Connections\BaseConnectionStorage;
use Core\WebSocket\Connections\ConnectionEntity;
use Core\WebSocket\Connections\SubscriptionEntity;
use Core\WebSocket\Connections\SubscriptionQuery;
use Core\WebSocket\Contracts\Subscribable;
use Core\WebSocket\Exceptions\SubscriptionAuthException;
use Core\WebSocket\Traits\InteractsWithConnection;
use Core\WebSocket\Traits\InteractsWithMessage;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use JsonException;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServerInterface;
use ReflectionClass;
use ReflectionException;

abstract class BaseGraphQLWsHandler implements MessageComponentInterface, WsServerInterface
{
    use InteractsWithConnection;
    use InteractsWithMessage;

    public const WS_PROTOCOL = 'graphql-ws';

    public const CONNECTION_INIT = 'connection_init';
    public const GRAPH_QL_SUBSCRIBE = 'start';
    public const GRAPH_QL_UNSUBSCRIBE = 'stop';

    protected BaseConnectionStorage $connectionStorage;

    protected array $schema;

    public function __construct()
    {
        $this->setSchema();
        $this->setConnectionStorage();
    }

    abstract protected function setSchema(): void;

    abstract protected function setConnectionStorage(): void;

    public function onOpen(ConnectionInterface $conn): void
    {
        $conn->send(
            json_encode(
                [
                    'type' => 'connection_ack',
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        logger($e);

        $this->connectionStorage->removeConnection($conn);
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param MessageInterface $msg
     * @throws JsonException
     * @throws ReflectionException
     */
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg): void
    {
        $messagePayload = $this->getMessagePayload($msg);

        if ($this->isHandShake($messagePayload)) {
            $this->handleHandShake($conn, $messagePayload['payload']);

            return;
        }

        if ($this->isSubscription($messagePayload)) {
            $this->handleSubscription($conn, $messagePayload);

            return;
        }

        if ($this->isNotification($messagePayload)) {
            $this->handleNotification($messagePayload);
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param array $payload
     * @throws JsonException
     */
    protected function handleHandShake(ConnectionInterface $conn, array $payload): void
    {
        try {
            $this->connectionStorage->addConnection($conn, $payload);
        } catch (SubscriptionAuthException $e) {
            //no reason to spam logs with auth error
//            logger($e->getMessage());
            $conn->send($this->getResponseError($e));
            $conn->close();
        }
    }

    /** @throws JsonException */
    protected function getResponseError(SubscriptionAuthException|Exception $e): string
    {
        return json_encode(['type' => 'error', 'reason' => $e->getMessage()], JSON_THROW_ON_ERROR);
    }

    protected function handleSubscription(ConnectionInterface $conn, array $messagePayload): void
    {
        $this->connectionStorage->subscribe($conn, $messagePayload['payload'], $messagePayload['id']);
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     */
    protected function handleNotification(array $messagePayload): void
    {
        $context = $messagePayload['context'] ?? [];

        foreach ($this->connectionStorage->getConnectionsByMessagePayload($messagePayload) as $subscriber) {
            /** @var SubscriptionEntity $subscriptionEntity */
            $subscriptionEntity = $subscriber['subscription'];

            /** @var ConnectionEntity $connectionEntity */
            $connectionEntity = $subscriber['connection'];

            $message = $this->createGraphqlResponse(
                $connectionEntity,
                $subscriptionEntity->getSubscriptionQuery(),
                $context
            );

            if (!$this->hasError($message)) {
                $connectionEntity->getConnection()->send(json_encode($message, JSON_THROW_ON_ERROR));
            } else {
                logger('error while broadcasting event', $message);
            }
        }
    }

    /** @throws ReflectionException */
    protected function createGraphqlResponse(ConnectionEntity $entity, SubscriptionQuery $query, array $context): array
    {
        $options = [
            'context' => $context,
            'schema' => $this->schema,
            'operationName' => $query->operationName(),
        ];

        //todo: нужно зарефакторить эту часть. суть в том, что надо сетить пользователя до запроса, и удалять после запроса
        /** @var BaseAuthenticatable|Subscribable $user */
        if ($user = $entity->getUser()) {
            $this->getGuard($user::GUARD)->setUser($user);
        }

        //temporary decision
        $gql = app('graphql');
//        $gql->clearSchemas();
        $gql->clearTypes();
        $gql->addTypes(config('graphql.types'));

        $response = $gql->query(
            $query->query(),
            $query->variables(),
            $options,
        );

        if ($user) {
            $authGuard = $this->getGuard($user::GUARD);

            $reflectionClass = new ReflectionClass($authGuard);
            $reflectionProperty = $reflectionClass->getProperty('user');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($authGuard, null);
        }

        return [
            'id' => $query->getId(),
            'type' => 'data',
            'payload' => $response,
        ];
    }

    protected function getGuard(string $name): Guard
    {
        return Auth::guard($name);
    }

    protected function hasError(array $message): bool
    {
        //todo make error response
        return Arr::has($message, 'payload.errors');
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->connectionStorage->removeConnection($conn);
    }

    public function getSubProtocols(): array
    {
        return [self::WS_PROTOCOL];
    }
}
