<?php

declare(strict_types=1);

namespace Core\GraphQL;

use GraphQL\Type\Schema;
use Illuminate\Foundation\Application;

class GraphQL extends \Rebing\GraphQL\GraphQL
{
    protected array $schemaCache = [];

    public function clearObjectFromTypes(): void
    {
        foreach ($this->getTypes() as $key => $type) {
            if (is_object($type)) {
                unset($this->types[$key]);
            }
        }
    }

    public function setApp(Application $app): void
    {
        $this->app = $app;
    }

    public function schema(?string $schemaName = null): Schema
    {
        if (!config('graphql.schema_cache')) {
            return parent::schema($schemaName);
        }

        return $this->schemaCache[$schemaName]
            ?? ($this->schemaCache[$schemaName] = parent::schema($schemaName));
    }
}
