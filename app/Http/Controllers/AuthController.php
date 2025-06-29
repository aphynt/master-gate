<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login()
    {
        return view('auth.login');
    }

    public function login_post(Request $request)
    {

        $credentials = $request->only('nrp', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // Periksa apakah statusenabled pengguna bernilai true
            if (Auth::user()->statusenabled == true) {
                return redirect()->route('dashboard.index')->with('success', 'Selamat Datang');
            } else {
                // Logout jika statusenabled adalah false
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->back()->with('info', 'Akun Anda tidak diaktifkan.');
            }
        }

        return redirect()->back()->with('info', 'NRP atau password salah');


    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar');
    }
}
