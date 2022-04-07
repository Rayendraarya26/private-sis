<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                $this->loginSuccess($data['results']);

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

    /**
     * loginSuccess
     * Set session if account exist or create new if not registered
     *
     * @param $data
     * @return void
     */
    private function loginSuccess($data)
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
