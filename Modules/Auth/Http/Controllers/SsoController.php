<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Auth\Http\Traits\SsoTrait;

class SsoController extends Controller
{
    use SsoTrait;

    /**
     * @throws \Throwable
     */
    public function callback(Request $request)
    {
        /*$state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class,
            'Invalid state value.'
        );*/

        $response = Http::asForm()->post(sprintf('%s/oauth/token', config('app.sso.server')), [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('app.sso.client_id'),
            'client_secret' => config('app.sso.client_secret'),
            'redirect_uri'  => config('app.sso.redirect_uri'),
            'code'          => $request->code,
        ]);

        // get user data
        return $this->loginSsoSuccess($response->json('access_token'));
    }

    public function redirect(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id'     => config('app.sso.client_id'),
            'redirect_uri'  => config('app.sso.redirect_uri'),
            'response_type' => 'code',
            'scope'         => '',
            'state'         => $state,
            // 'prompt' => '', // "none", "consent", or "login"
        ]);

        return redirect(sprintf('%s/oauth/authorize?', config('app.sso.server')) . $query);
    }
}
