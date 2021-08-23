<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SysUserGroup;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Http\Traits\AuthTraits;

class LoginController extends Controller
{
    use AuthTraits;

    public function index()
    {
        return view('auth::login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            "email" => 'required|email',
            "password" => 'required|min:4'
        ]);

        $credentials = [
            'user_email' => $request['email'],
            'password' => $request['password']
        ];
        $auth = Auth::attempt($credentials);
        if ($auth) {
            Auth::user()->user_last_login = date("Y-m-d H:i:s");
            Auth::user()->save();

            $group_selected = Auth::user()->user_group->where("ug_is_default", "yes")->first()->ug_group_id;
            $group_selected_name = Auth::user()->user_group->where("ug_is_default", "yes")->first()->group->group_name;
            $this->setAccess($group_selected, $group_selected_name);

            return redirect()->intended(route('dashboard'));
        } else {
            return redirect()->back()->withInput($request->only('email'))->withErrors(['status' => 'Kombinasi email dan password tidak sesuai']);
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $findUser = SysUser::where('user_email', $user->email)->first();
            if ($findUser) {
                Auth::login($findUser);

                Auth::user()->user_last_login = date("Y-m-d H:i:s");
                Auth::user()->save();

                $group_selected = Auth::user()->user_group->where("ug_is_default", "yes")->first()->ug_group_id;
                $group_selected_name = Auth::user()->user_group->where("ug_is_default", "yes")->first()->group->group_name;
                $this->setAccess($group_selected, $group_selected_name);

                return redirect()->intended(route('dashboard'));
            } else {
                return redirect(route('auth.login'))->withErrors(['status' => "Akun kamu belum terdaftar di " . env('APP_NAME')]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function resendValidation()
    {
        if (auth()->check()) {
            if (auth()->user()->user_is_active == "yes") {
                return redirect(route('dashboard'));
            }
            return view("auth::resend_validation");
        }
        return redirect(route('auth.login'));
    }

    public function handleResendValidation()
    {
        if (auth()->check()) {
            // ================ Send Email ================
            $structEmail = new EmailStruct();
            $structEmail->subject = "Verifikasi Akun";
            $structEmail->body = view('auth::mails.resend_validation')
                ->with([
                    'name' => auth()->user()->user_fullname,
                    'link' => route('auth.verify', encrypt(auth()->user()->user_token))
                ])->render();
            $structEmail->to = auth()->user()->user_email;
            sendEmail($structEmail);
            // ================ END Send Email ================

            return redirect()->back()->with("message", "Email telah dikirim, silakan cek email anda (inbox/promotion/spam)");
        } else {
            abort(401);
        }
    }

    public function verifyValidation($token)
    {
        try {
            $token = decrypt($token);
            $dataUser = SysUser::where("user_token", $token)->first();
            if ($dataUser) {
                $dataUser->user_is_active = "yes";
                $dataUser->user_active_at = Carbon::now();
                $dataUser->user_token = null;
                $dataUser->save();
                return redirect(route('auth.login'))->with("message", "Akun telah aktif");
            } else {
                return redirect(route('auth.login'))->withErrors(["status" => "Link sudah kadaluarsa"]);
            }

        } catch (Exception $e) {
            abort(404);
        }
    }

    public function switchRole(Request $request) // Khusus yang sudah login
    {
        if (Auth::check()) {
            $request->validate(['modal_group_id' => 'required']);
            $group_id = $request['modal_group_id'];
            $exist = SysUserGroup::where("ug_user_id", Auth::id())->where("ug_group_id", $group_id)->first();
            if ($exist) {
                $group_selected = $group_id;
                $group_selected_name = $exist->group->group_name;
                $this->setAccess($group_selected, $group_selected_name);
                return redirect(RouteServiceProvider::HOME)->with('message', "Berhasil ganti role");
            }
        }
        abort(401);

    }

    public function logout()  // Khusus yang sudah login
    {
        session()->flush();
        Auth::logout();
        return redirect(route("auth.login"));
    }
}
