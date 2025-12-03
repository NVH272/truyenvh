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
        // Chuẩn hóa email: trim và lowercase TRƯỚC khi validate
        $email = strtolower(trim($request->email));
        
        // Merge email đã normalize vào request để validation kiểm tra đúng
        $request->merge(['email' => $email]);
        
        // Validate dữ liệu
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed', // cần field password_confirmation
        ], [
            'email.unique' => 'Email này đã được sử dụng. Vui lòng chọn email khác.',
            'email.email' => 'Email không hợp lệ.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Tạo user với email đã được normalize
        $user = User::create([
            'name'     => trim($request->name),
            'email'    => $email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // role mặc định
        ]);

        // GỬI MAIL XÁC THỰC
        $user->sendEmailVerificationNotification();

        // Cho phép đăng nhập ngay, nhưng khuyến khích xác thực email
        return redirect()->route('login.form')
            ->with('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay. Vui lòng kiểm tra email để xác thực tài khoản để sử dụng đầy đủ các chức năng.');

        // Đăng nhập luôn sau khi đăng ký (tuỳ bạn)
        // Auth::login($user);

        // return redirect()->route('home')
        //     ->with('success', 'Đăng ký thành công!');
    }

    // 3. Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 4. Xử lý đăng nhập người dùng
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Chuẩn hóa email trước khi đăng nhập (khớp với cách lưu trong database)
        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Nếu tài khoản bị khóa (nếu bạn có is_active)
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ]);
            }

            // Cho phép đăng nhập dù chưa xác thực email
            // User chưa verify vẫn có thể sử dụng các chức năng như người chưa đăng nhập
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
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
