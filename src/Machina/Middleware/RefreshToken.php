<?php

namespace Code16\Machina\Middleware;

class RefreshToken
{  
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        $refreshedToken = auth()->refresh();
        $response->headers->set('Authorization', 'Bearer '.$refreshedToken);

        return $response;
    }
}