<?php

namespace App\Http\Middleware;

use App\Models\Institute;
use Closure;
use Illuminate\Support\Facades\URL;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $redirectTo
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(! Institute::whereSubdomain(request('subdomain'))->exists()) {
            abort(404);
        }
        URL::defaults(['subdomain' => request('subdomain')]);

        return $next($request);
    }
}
