<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @param string $redirect_to
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null, $redirect_to = '/')
    {
        if (Auth::guard($guard)->check()) {
//            return redirect(RouteServiceProvider::HOME);
            return redirect($redirect_to);
        }

        return $next($request);
    }
}
