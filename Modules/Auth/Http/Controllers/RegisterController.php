<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class RegisterController extends Controller
{


    public function index()
    {
        return view('auth::register');
    }

    public function handleRegister(Request $request)
    {
        $request->validate([
            "fullname" => 'required|string',
            "email"    => 'required|email|unique:App\Models\BbkkpSis\SysUser,user_email',
            "password" => 'required|confirmed|min:4'
        ]);

        /* Create User
        1. Insert into sys_user
        2. Insert into sys_user_group
        3. Send Email Verification
        */

        // 1
        try {
            DB::beginTransaction();
            $user = new SysUser();
            $user->user_email = strtolower($request['email']);
            $user->user_fullname = ucwords($request['fullname']);
            $user->user_password = bcrypt($request['password']);
            $user->user_token = Str::random(20);
            $user->user_is_active = "no";
            $user->user_is_banned = "no";
            $user->user_created_at = Carbon::now();
            $user->save();

            $ug = new SysUserGroup();
            $ug->ug_user_id = $user->user_id;
            $ug->ug_group_id = 2;
            $ug->ug_is_default = "yes";
            $ug->ug_created_at = Carbon::now();
            $ug->save();

            $sisPelanggan = new SisPelanggan();
            $sisPelanggan->user_id = $user->user_id;
            $sisPelanggan->cust_email = $user->user_email;
            $sisPelanggan->save();
            DB::commit();


            // ================ Send Email ================
            $structEmail = new EmailStruct();
            $structEmail->subject = "Pendaftaran berhasil";
            $structEmail->body = view('auth::mails.register_success')
                ->with([
                    'name' => $user->user_fullname,
                    'link' => route('auth.verify', encrypt($user->user_token))
                ])->render();
            $structEmail->to = $user->user_email;
            sendEmail($structEmail);
            // ================ END Send Email ================

            return redirect()->back()->with('message', "Pendaftaran berhasil, silakan cek email anda");
        } catch (Throwable $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->except("_token"))->withErrors(["status" => $e->getMessage()]);
        }
    }
}
