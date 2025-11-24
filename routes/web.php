<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController; // nếu có

// Trang chủ (ví dụ)
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// ĐĂNG KÝ người dùng
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form'); // GET: hiển thị form
Route::post('/register', [AuthController::class, 'register'])->name('register');            // POST: xử lý đăng ký

// ĐĂNG NHẬP người dùng
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');         // GET: hiển thị form
Route::post('/login', [AuthController::class, 'login'])->name('login');                     // POST: xử lý đăng nhập

// QUÊN MẬT KHẨU
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request'); // GET: hiển thị form
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');      // POST: gửi link đặt lại mật khẩu

// ĐĂNG XUẤT
// (thực tế nên dùng POST là chính, nhưng bạn yêu cầu cả GET & POST thì ta cho trùng controller)
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// KHU VỰC ADMIN – dùng middleware kiểm tra quyền
Route::middleware(['auth', 'isAdmin'])->group(function () {
    // Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Các route admin khác...
    // Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
});
