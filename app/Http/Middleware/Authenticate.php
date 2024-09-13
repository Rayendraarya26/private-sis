<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Modules\Auth\Http\Traits\SsoTrait;

class Authenticate
{
    use SsoTrait;


    public function handle(Request $request, Closure $next)
    {
        if (config('app.sso.is_enabled')) {
            return $this->ssoMiddleware($request, $next);
        } else {
            return $this->normalMiddleware($request, $next);
        }
    }

    private function normalMiddleware(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->user()->user_is_banned == "yes") {
                auth()->logout();
                return redirect(route('auth.login'))->withErrors(['status' => "Akun anda telah di blokir oleh admin"]);
            } elseif (auth()->user()->user_is_active == "yes") {
                return $next($request);
            } else {
                return redirect(route('auth.resend_validation'));
            }
        } else {
            return redirect()->guest(route('auth.login'));
        }
    }

    private function ssoMiddleware(Request $request, Closure $next)
    {
        if (auth()->check()) {
            return $this->normalMiddleware($request, $next);
        } elseif (Cookie::get('access_token')) {
            return $this->loginSsoSuccess(Cookie::get('access_token'));
        } else {
            return redirect('/auth/sso/redirect');
        }
    }
}
