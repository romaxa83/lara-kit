<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class TelescopeIpAccessMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('telescope.ip-restriction')) {
            return $next($request);
        }

        $needle = $request->ip();
        if (in_array($needle, config('telescope.allowed-ips'), true)) {
            return $next($request);
        }

        throw new UnauthorizedException('You will not pass!');
    }
}
