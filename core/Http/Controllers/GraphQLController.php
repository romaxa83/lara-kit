<?php

declare(strict_types=1);

namespace Core\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laragraph\Utils\RequestParser;
use Rebing\GraphQL\GraphQL;

class GraphQLController extends \Rebing\GraphQL\GraphQLController
{
    /**
     * @throws BindingResolutionException
     */
    public function query(
        Request $request,
        RequestParser $parser,
        Repository $config,
        GraphQL $graphql
    ): JsonResponse {
        return parent::query($request, $parser, $config, $graphql);
    }
}
