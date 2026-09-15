<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpFoundation\Response;

class EnsureMizanApiAccess
{
    /** Permit browser sessions and require the explicit ability for API tokens. */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token !== null && ! $request->user()->tokenCan('mizan3g:api')) {
            throw new MissingAbilityException('mizan3g:api');
        }

        return $next($request);
    }
}
