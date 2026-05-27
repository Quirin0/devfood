<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $validEmail = (string) config('admin.email');
        $validPassword = (string) config('admin.password');

        if (
            $credentials['email'] !== $validEmail ||
            $credentials['password'] !== $validPassword
        ) {
            return back()->withInput()->withErrors([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        $request->session()->put('is_admin', true);
        $request->session()->regenerate();

        return redirect()->route('admin.google.edit');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

