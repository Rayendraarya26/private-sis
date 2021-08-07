<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->user()->user_is_active == "yes" && auth()->user()->user_is_banned == "no") {
                return $next($request);
            } else {
                return redirect(route('auth.resend_validation'));
            }
        } else {
            return redirect(route('auth.login'));
        }
    }
}
