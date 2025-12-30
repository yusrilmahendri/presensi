<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect('/admin');
            }
            return redirect()->route('karyawan.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // Cari user berdasarkan username (admin), NIK, NIP, atau email
        $user = \App\Models\User::where('username', $request->login)
            ->orWhere('nik', $request->login)
            ->orWhere('nip', $request->login)
            ->orWhere('email', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'Username/NIK/NIP atau password salah.',
            ])->withInput($request->only('login'));
        }

        // Login user
        Auth::login($user, $request->remember ?? false);

        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->isAdmin()) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended(route('karyawan.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
