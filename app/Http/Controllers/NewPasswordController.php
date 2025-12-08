<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    // Hiển thị form reset mật khẩu (chính form bạn gửi)
    public function create(Request $request, $token)
    {
        return view('auth.resetPassword', [
            'request' => $request,
            'token'   => $token,
        ]);
    }

    // Xử lý lưu mật khẩu mới
    public function store(Request $request)
    {
        // Lấy email từ query (link reset) để tránh phải nhập lại
        if ($request->query('email') && !$request->input('email')) {
            $request->merge(['email' => $request->query('email')]);
        }

        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.form')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
