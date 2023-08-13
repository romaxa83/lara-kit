<?php

namespace Core\Providers;

use Core\GraphQL\GraphQL;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Rebing\GraphQL\GraphQLServiceProvider;

class GraphQLConfigProvider extends GraphQLServiceProvider
{
    public function registerGraphQL(): void
    {
        $this->app->singleton(GraphQL::class, function (Container $app): GraphQL {
            $config = $app->make(Repository::class);

            $graphql = new GraphQL($app, $config);

            $this->applySecurityRules($config);

            //$this->bootSchemas($graphql);

            return $graphql;
        });

        $this->app->alias(GraphQL::class, 'graphql');

        $this->app->afterResolving(GraphQL::class, function (GraphQL $graphQL): void {
            $this->bootTypes($graphQL);
        });
    }
}
