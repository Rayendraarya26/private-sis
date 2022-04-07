<?php

namespace Modules\Auth\Http\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Jasny\SSO\Broker\Broker;

trait SsoBrokerTrait
{
    public function attach(): Broker|Redirector|RedirectResponse|View
    {
        $broker = new Broker(
            env('SSO_SERVER') . '/sso/attach',
            env('SSO_BROKER_ID'),
            env('SSO_BROKER_SECRET')
        );

        // Handle error from SSO server
        if (isset($_GET['sso_error'])) {
            return view("errors.custom")->with([
                'code'    => 400,
                'info'    => "SSO ERROR",
                'message' => $_GET['sso_error'],
            ]);
        }

        // Handle verification from SSO server
        if (isset($_GET['sso_verify'])) {
            $broker->verify($_GET['sso_verify']);

            $url         = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $redirectUrl = preg_replace('/sso_verify=\w+&|[?&]sso_verify=\w+$/', '', $url);
            return redirect($redirectUrl);
        }

        // Attach through redirect if the client isn't attached yet.
        if (!$broker->isAttached()) {
            $returnUrl = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $attachUrl = $broker->getAttachUrl(['return_url' => $returnUrl]);

            return redirect($attachUrl);
        }

        return $broker;
    }
}
