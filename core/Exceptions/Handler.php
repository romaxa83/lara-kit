<?php

namespace Core\Exceptions;

use GraphQL\Error\Error;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(static function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response
    {
        if ($e instanceof Error) {
            return $this->formatToGraphQL($e);
        }

        return parent::render($request, $e);
    }

    protected function formatToGraphQL(Throwable $e): JsonResponse
    {
        $body = [
            'errors' => [
                call_user_func(config('graphql.error_formatter'), $e),
            ],
        ];

        $headers = config('graphql.headers', []);
        $jsonOptions = config('graphql.json_encoding_options', 0);

        return response()->json($body, 200, $headers, $jsonOptions);
    }
}
