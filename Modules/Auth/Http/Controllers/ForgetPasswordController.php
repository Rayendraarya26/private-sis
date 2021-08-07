<?php

namespace Modules\Auth\Http\Controllers;

use App\Models\BbkkpSis\SysUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Auth\Emails\ForgetPassword;

class ForgetPasswordController extends Controller
{
    public function index()
    {
        return view("auth::forget_password");
    }

    public function handleResetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $dataUser = SysUser::where('user_email', $request['email'])->first();
        if (!$dataUser) {
            return redirect()->back()->withErrors(['message' => "Email tidak terdaftar"]);
        } else {
            $dataUser->user_token = Str::random(20);
            $dataUser = $dataUser->save();
            Mail::to($dataUser->user_email)->send(new ForgetPassword($dataUser));
            return redirect()->back()->with("message", "Email telah dikirim, silakan cek email anda (inbox/promotion/spam)");
        }
    }

    public function resetPassword($token)
    {
        try {
            $token = decrypt($token);
            $dataUser = SysUser::where("user_token", $token)->first();
            if ($dataUser) {
                return view("auth::new_password")->with(['data' => $dataUser]);
            } else {
                return redirect(route('auth.login'))->withErrors(["status" => "Link sudah kadaluarsa"]);
            }

        } catch (Exception $e) {
            abort(404);
        }
    }

    public function handleNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4|confirmed'
        ]);

        $data = SysUser::where("user_email", $request['email'])->firstOrFail();
        $data->user_password = bcrypt($request['password']);
        $data->save();

        return redirect(route('auth.login'))->with("message", "Kata sandi berhasil diperbarui");
    }
}
