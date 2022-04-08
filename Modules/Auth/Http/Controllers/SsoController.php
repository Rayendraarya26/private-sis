<?php

namespace Modules\Auth\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Jasny\SSO\Broker\Broker;
use Modules\Auth\Http\Traits\AuthTraits;
use Modules\Auth\Http\Traits\SsoBrokerTrait;

class SsoController extends Controller
{
    use SsoBrokerTrait, AuthTraits;

    public function login()
    {
        $broker = $this->attach();
        if (!($broker instanceof Broker)) {
            return $broker;
        } else {
            try {
                $data = $broker->request("GET", "/sso/info");
                $this->loginSsoSuccess($data['results']);

                return redirect()->intended(route('dashboard'));
            } catch (Exception $e) {
                try {
                    $errorMessage = json_decode($e->getMessage());
                    if ($errorMessage->code == 403 && $errorMessage->message == "Akun belum login") {
                        return redirect(env("SSO_SERVER") . "/sso/login?key=" . $broker->getBearerToken());
                    }

                    return view("errors.custom")->with([
                        'code'    => 400,
                        'info'    => "BAD REQUEST",
                        'message' => 'Please dont worrry :D ' . $e->getMessage(),
                    ]);
                } catch (Exception $e) {
                    return view("errors.custom")->with([
                        'code'    => 500,
                        'info'    => "SERVER ERROR",
                        'message' => 'Please dont worrry :D ' . $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function logout()
    {
        $broker = $this->attach();
        if (!($broker instanceof Broker)) {
            return $broker;
        } else {
            try {
                $broker->request("GET", "/sso/logout");
                return redirect('/');
            } catch (Exception $e) {
                return view("errors.custom")->with([
                    'code'    => 500,
                    'info'    => "SERVER ERROR",
                    'message' => 'Please dont worrry :D ' . $e->getMessage(),
                ]);
            }
        }
    }
}
