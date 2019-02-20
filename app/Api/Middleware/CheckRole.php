<?php

namespace App\Api\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return mixed
     * @throws ApiException
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()->hasRole($role)) {
            throw new ApiException(null, 404);
        }

        return $next($request);
    }
}
