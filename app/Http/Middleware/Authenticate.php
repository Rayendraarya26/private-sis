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
            if (auth()->user()->user_is_banned == "yes") {
                auth()->logout();
                return redirect(route('auth.login'))->withErrors(['status' => "Akun anda telah di blokir oleh admin"]);
            } else if (auth()->user()->user_is_active == "yes") {
                return $next($request);
            } else {
                return redirect(route('auth.resend_validation'));
            }
        } else {
            return redirect(route('auth.login'));
        }
    }
}
