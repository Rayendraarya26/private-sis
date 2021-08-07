<?php

namespace Modules\Auth\Http\Controllers;

use App\Models\BbkkpSis\SysUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Emails\ResendValidation;
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
            $dataMenu = DB::select(DB::RAW("
                SELECT DISTINCT menu_name, menu_id, menu_parent_id, menu_icon, sma.action_controller
                FROM sys_menu
                         JOIN sys_menu_action sma ON sys_menu.menu_id = sma.actiON_menu_id AND sma.action_name = 'index'
                         JOIN sys_group_permission sgp ON sma.action_id = sgp.action_id
                WHERE sgp.group_id = '$group_selected' AND menu_is_active = 'yes'
                ORDER BY menu_parent_id, menu_order, menu_name
            "));
            $menuAction = [];
            $permission = DB::select(DB::RAW("
                SELECT action_controller FROM sys_group_permission
                JOIN sys_menu_action sma ON sys_group_permission.action_id = sma.action_id
                WHERE group_id = '$group_selected'
            "));
            foreach ($permission as $p) {
                array_push($menuAction, $p->action_controller);
            }
            $dataSession = [
                'group_selected' => $group_selected,
                'group_selected_name' => $group_selected_name,
                'group_available' => Auth::user()->user_group,
                'permission' => $menuAction,
                'menu' => $this->buildTree($dataMenu),
            ];

            session($dataSession);

            return redirect()->intended(route('dashboard'));
        } else {
            return redirect()->back()->withErrors(['status' => 'Kombinasi email dan password tidak sesuai']);
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
                return redirect()->intended(route('dashboard'));
            } else {
                return redirect(route('auth.login'))->withErrors(['status' => "Akun kamu belum terdaftar di BBKKP SIS"]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function resendValidation()
    {
        if (auth()->user()->user_is_active == "yes"){
            return redirect(route('dashboard'));
        }
        return view("auth::resend_validation");
    }

    public function handleResendValidation()
    {

        Mail::to(auth()->user()->user_email)->send(new ResendValidation(auth()->user()));
        return redirect()->back()->with("message", "Email telah dikirim, silakan cek email anda (inbox/promotion/spam)");
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

    public function logout()
    {
        Auth::logout();
        return redirect(route("auth.login"));
    }
}
