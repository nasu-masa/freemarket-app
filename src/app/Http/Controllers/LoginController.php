<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        // 認証試行
        if (Auth::attempt($request->only('email', 'password')))
        {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail())
            {
                return redirect()->route('verification.notice');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}