<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // 2. Xử lý đăng ký người dùng
    public function register(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // cần field password_confirmation
        ]);

        // Tạo user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // role mặc định
        ]);

        // Đăng nhập luôn sau khi đăng ký (tuỳ bạn)
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký thành công!');
    }

    // 3. Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 4. Xử lý đăng nhập người dùng
    public function login(Request $request)
    {
        // Validate
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember'); // checkbox nhớ đăng nhập

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // chống session fixation

            // Nếu muốn phân quyền theo role, có thể check ở đây
            // $user = Auth::user();
            // if ($user->role == 'admin') return redirect()->route('admin.dashboard');

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])
            ->onlyInput('email');
    }

    // 5. Xử lý đăng xuất người dùng
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('success', 'Đã đăng xuất!');
    }

    // 6. Hiển thị form quên mật khẩu
    public function showForgotPasswordForm()
    {
        return view('auth.forgotPassword');
    }
}
