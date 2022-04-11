<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Jasny\SSO\Broker\Broker;
use Modules\Auth\Http\Traits\SsoBrokerTrait;

class Authenticate
{
    use SsoBrokerTrait;

    public function handle(Request $request, Closure $next)
    {
        $broker = $this->attach();
        if (!($broker instanceof Broker)) {
            auth()->logout();
            session()->flush();
            if ($broker instanceof RedirectResponse) {
                return redirect($broker->getTargetUrl());
            } else {
                return redirect()->guest(route('auth.login'));
            }
        } else {
            try {
                $data          = $broker->request("GET", "/sso/info");
                $isLoginBefore = true;
                if (!auth()->check()) {
                    $this->loginSsoSuccess($data['results']);
                    $isLoginBefore = false;
                }
                return $this->checkClientAuth($request, $next, $isLoginBefore);
            } catch (\Exception $e) {
                $errorMessage = json_decode($e->getMessage());
                if ($errorMessage->code == 403 && $errorMessage->message == "Akun belum login") {
                    return redirect(env("SSO_SERVER") . "/sso/login?key=" . $broker->getBearerToken());
                }

                $broker->clearToken();
                auth()->logout();
                session()->flush();
                return redirect(url("/auth/sso/login"));
            }
        }
    }

    private function checkClientAuth(Request $request, Closure $next, $isLoginBefore = false)
    {
        if (auth()->user()->user_is_banned == "yes") {
            auth()->logout();
            return redirect(route('auth.login'))->withErrors(['status' => "Akun anda telah di blokir oleh admin"]);
        } else if (auth()->user()->user_is_active == "yes") {
            if ($isLoginBefore) {
                return $next($request);
            } else {
                return redirect()->route('dashboard');
            }
        } else {
            return redirect(route('auth.login'))->withErrors(['status' => "Akun belum aktif, mohon hubungi admin"]);
        }
    }
}
