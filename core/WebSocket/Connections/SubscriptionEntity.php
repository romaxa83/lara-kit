<?php

namespace Core\WebSocket\Connections;

use InvalidArgumentException;

class SubscriptionEntity
{
    public const PARSE_PATTERN = <<<REGEXP
/subscription?\s*\w+?\s*{\s+(?<subscription>\w+)\s*(\(\s*channel:*\s*["'](?<channel>[^"']+))?/
REGEXP;

    private SubscriptionQuery $subscriptionQuery;
    private string $subscriptionName;
    private ?string $channel;

    protected function __construct(array $payload, int|string $id)
    {
        $this->resolveSubscriptionName($payload['query']);

        $this->subscriptionQuery = new SubscriptionQuery($id, $payload);
    }

    private function resolveSubscriptionName(string $query): void
    {
        $query = preg_replace('/\\n/', '', $query);

        preg_match(self::PARSE_PATTERN, $query, $matches);

        if (!isset($matches['subscription'])) {
            throw new InvalidArgumentException('Not a valid subscription query');
        }

        $this->subscriptionName = $matches['subscription'];
        $this->channel = $matches['channel'] ?? null;
    }

    public static function makeFromPayload(array $payload, int|string $id): self
    {
        return new self($payload, $id);
    }

    public function getSubscriptionQuery(): SubscriptionQuery
    {
        return $this->subscriptionQuery;
    }

    public function hasSubscriptionName(): bool
    {
        return isset($this->subscriptionName);
    }

    public function getSubscriptionName(): string
    {
        return $this->subscriptionName;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }
}
