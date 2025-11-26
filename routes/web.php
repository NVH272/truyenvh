<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComicController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::view('/', 'home')->name('home');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    // Đăng ký
    Route::get('/register', 'showRegisterForm')->name('register.form');
    Route::post('/register', 'register')->name('register');

    // Đăng nhập
    Route::get('/login', 'showLoginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');

    // Quên mật khẩu (chỉ có trang form)
    Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');

    // Đăng xuất (GET + POST để tiện cho form & link)
    Route::get('/logout', 'logout')->name('logout.get');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Tất cả route admin đều prefix /admin và yêu cầu middleware auth + isAdmin.
| Giữ tên route cho resource Category / Comic là "categories.*" và "comics.*"
| để khớp với trong controller.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'isAdmin'])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Quản lý thể loại
        Route::resource('categories', CategoryController::class);

        // Quản lý truyện
        Route::resource('comics', ComicController::class);
    });
