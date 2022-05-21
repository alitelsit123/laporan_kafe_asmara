<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin() {
        return view('login');
    }
    public function login() {
        request()->validate([
            'identifier' => ['required'],
            'password' => ['required']
        ]);

        $credentialEmail = [
            'email' => request('identifier'),
            'password' => request('password')
        ];
        $credentialUsername = [
            'username' => request('identifier'),
            'password' => request('password')
        ];

        if(auth()->attempt($credentialEmail)) {
            session()->regenerate();
            return redirect()->intended('/');
        } else if(auth()->attempt($credentialUsername)) {
            session()->regenerate();
            return redirect()->intended('/');
        }

        return redirect('/login')->withErrors([
            'credentials' => 'Username / Password Salah!'
        ]);
    }



    public function logout() {
        if(auth()->check()) {
            auth()->logout();
        }
        return redirect('/login');
    }
}
