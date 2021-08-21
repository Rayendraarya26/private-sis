<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        return view('home::account.profile');
    }

    public function editPassword()
    {
        return view("home::account.change_password");
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|min:4',
            'new_password' => 'required|min:4|confirmed',
        ]);

        if (Hash::check($request['current_password'], Auth::user()->user_password)) {
            Auth::user()->user_password = bcrypt($request['new_password']);
            Auth::user()->save();
            return redirect()->back()->with("message", "Kata sandi berhasil diperbarui");
        } else {
            return redirect()->back()->withErrors(['message' => "Kata sandi sekarang tidak sesuai"]);
        }
    }

    public function editProfile()
    {
        return view("home::account.update_profile");
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
        ]);

        $dataUser = Auth::user();
        $dataUser->user_fullname = $request['fullname'];
        $dataUser->save();

        return redirect()->back()->with('message', "Profil berhasil diperbarui");
    }
}
