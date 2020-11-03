<?php

namespace Code16\Machina\Middleware;

class RefreshToken
{  
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        $refreshedToken = auth()->refresh();
        
        $response->headers->set('Authorization', 'Bearer '.$refreshedToken);

        return $response;
    }
}