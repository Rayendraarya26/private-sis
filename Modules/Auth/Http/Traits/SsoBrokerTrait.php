<?php

namespace Modules\Auth\Http\Traits;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jasny\SSO\Broker\Broker;

trait SsoBrokerTrait
{
    use AuthTraits;
    public function attach(): Broker|Redirector|RedirectResponse|View
    {
        $broker = new Broker(
            config('app.sso_server') . '/sso/attach',
            config('app.sso_broker_id'),
            config('app.sso_broker_secret')
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


    /**
     * loginSsoSuccess: Set session if account exist or create new if not registered
     *
     * @param $data : menerima data dari SSO server
     * @return void
     */
    public function loginSsoSuccess($data)
    {
        $dataUser = SysUser::with(['sis_pelanggan', 'sys_user_group'])->where('user_email', $data['email'])->first();
        if (!empty($dataUser)) {
            Auth::loginUsingId($dataUser->user_id);
        } else {
            DB::beginTransaction();
            $user                  = new SysUser();
            $user->user_email      = strtolower($data['email']);
            $user->user_fullname   = ucwords($data['fullname']);
            $user->user_password   = null;
            $user->user_token      = null;
            $user->user_is_active  = "yes";
            $user->user_is_banned  = "no";
            $user->user_picture    = $data['profile'];
            $user->user_created_at = Carbon::now();
            $user->save();

            $ug                = new SysUserGroup();
            $ug->ug_user_id    = $user->user_id;
            $ug->ug_group_id   = 3;
            $ug->ug_is_default = "yes";
            $ug->ug_created_at = Carbon::now();
            $ug->save();

            $sisPelanggan                = new SisPelanggan();
            $sisPelanggan->user_id       = $user->user_id;
            $sisPelanggan->cust_email    = $user->user_email;
            $sisPelanggan->cust_nomor_hp = $data['phone'];
            $sisPelanggan->cust_nama     = $data['company_name'];
            $sisPelanggan->cust_alamat   = $data['company_address'];
            $sisPelanggan->save();
            DB::commit();

            // ================ Send Email ================
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Pendaftaran berhasil";
            $structEmail->body    = view('auth::mails.register_success')
                ->with([
                    'name' => $user->user_fullname,
                    'link' => route('auth.verify', encrypt($user->user_token))
                ])->render();
            $structEmail->to      = $user->user_email;
            sendEmail($structEmail);
            // ================ END Send Email ================

            Auth::loginUsingId($user->user_id);
        }

        Auth::user()->user_last_login = date("Y-m-d H:i:s");
        Auth::user()->save();

        $group_selected      = Auth::user()->user_group->where("ug_is_default", "yes")->first()->ug_group_id;
        $group_selected_name = Auth::user()->user_group->where("ug_is_default", "yes")->first()->group->group_name;
        $this->setAccess($group_selected, $group_selected_name);
    }
}
