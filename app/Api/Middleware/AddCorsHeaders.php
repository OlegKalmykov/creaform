<?php

namespace App\Api\Middleware;

use Closure;

class AddCorsHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (strpos($request->getPathInfo(), '/api/') !== false) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', env('CORS_DOMAIN'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $next($request);
    }
}
