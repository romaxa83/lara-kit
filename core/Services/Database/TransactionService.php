<?php

namespace Core\Services\Database;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionService
{
    private array $connections = [];

    /**
     * @param Closure $action
     * @param array<Connection> $connections
     * @return mixed
     * @throws Throwable
     */
    public function handle(Closure $action, array $connections = []): mixed
    {
        $this->setConnections($connections);

        $this->beginTransaction();

        try {
            $result = $action();

            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollback();

            logger($e);

            throw $e;
        }
    }

    private function setConnections(array $connections): void
    {
        $this->connections = empty($connections)
            ? [DB::connection()]
            : $connections;
    }

    /**
     * @throws Throwable
     */
    private function beginTransaction(): void
    {
        foreach ($this->getConnections() as $connection) {
            $connection->beginTransaction();
        }
    }

    /**
     * @return array<Connection>
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @throws Throwable
     */
    private function commit(): void
    {
        foreach (array_reverse($this->getConnections()) as $connection) {
            $connection->commit();
        }
    }

    /**
     * @throws Throwable
     */
    private function rollback(): void
    {
        foreach (array_reverse($this->getConnections()) as $connection) {
            $connection->rollback();
        }
    }
}
