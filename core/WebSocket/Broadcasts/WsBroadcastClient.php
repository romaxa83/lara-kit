<?php

namespace Core\WebSocket\Broadcasts;

use WebSocket\BadOpcodeException;
use WebSocket\Client;
use WebSocket\Message\Factory;

class WsBroadcastClient
{
    protected ?int $port;
    protected string $subscriptionEndpoint;
    private string $host;

    public function __construct()
    {
        $this->host = config('websockets.host');
        $this->port = config('websockets.port');
    }

    public function setSubscriptionEndpoint(string $subscriptionEndpoint): self
    {
        $this->subscriptionEndpoint = $subscriptionEndpoint;

        return $this;
    }

    /**
     * @throws BadOpcodeException
     */
    public function send(string $data): void
    {
        $this->getClient()->send($data);
    }

    protected function getClient(): Client
    {
        return new Client(
            $this->getConnectionUri(),
            [
                'headers' => $this->getHeaders(),
            ]
        );
    }

    protected function getConnectionUri(): string
    {
        if ($this->port) {
            return sprintf(
                '%s:%d/%s',
                $this->host,
                $this->port,
                $this->subscriptionEndpoint,
            );
        }

        return sprintf(
            '%s/%s',
            $this->host,
            $this->subscriptionEndpoint,
        );
    }

    protected function getHeaders(): array
    {
        return [
            'Sec-WebSocket-Protocol' => 'graphql-ws',
        ];
    }

    public function receive(): string|Factory
    {
        return $this->getClient()->receive();
    }
}
