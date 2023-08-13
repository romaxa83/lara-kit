<?php

namespace Core\WebSocket\Providers;

use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\Server\Logger\WebsocketsLogger;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\NullOutput;

class GraphQLWebsocketProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        foreach (config('websockets.routes') as $uri => $handler) {
            WebSocketsRouter::get($uri, $handler);
        }
    }

    public function register(): void
    {
        $this->app->singleton(WebsocketsLogger::class, function () {
            return new WebsocketsLogger(new NullOutput());
        });
    }
}
