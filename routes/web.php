<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Trang nhắc xác thực email (nếu bạn muốn dùng)
Route::get('/email/verify', function (Request $request) {
    $user = $request->user();

    // Tự động gửi email xác thực nếu user chưa verify và chưa có thông báo trong session
    if (!$user->hasVerifiedEmail()) {
        // Chỉ gửi nếu chưa có flag trong session (tránh gửi lại khi refresh)
        if (!$request->session()->has('verification_email_auto_sent')) {
            $user->sendEmailVerificationNotification();
            $request->session()->put('verification_email_auto_sent', true);

            return view('auth.verify-email')->with('info', 'Email xác thực đã được gửi tự động đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư.');
        }
    }

    return view('auth.verify-email');
})->middleware(['auth', 'throttle:6,1'])->name('verification.notice');

// Link trong email sẽ trỏ vào đây
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    // Kiểm tra hash và chữ ký
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Liên kết xác thực không hợp lệ.');
    }

    // Kiểm tra chữ ký URL
    if (!URL::hasValidSignature($request)) {
        abort(403, 'Liên kết xác thực đã hết hạn hoặc không hợp lệ.');
    }

    // Nếu đã xác thực rồi
    if ($user->hasVerifiedEmail()) {
        // Nếu user đã đăng nhập, redirect về home, nếu chưa thì về login
        if (Auth::check() && Auth::id() == $user->id) {
            return redirect()->route('home')
                ->with('info', 'Email của bạn đã được xác thực trước đó.');
        }
        return redirect()->route('login.form')
            ->with('info', 'Email của bạn đã được xác thực trước đó. Bạn có thể đăng nhập.');
    }

    // Xác thực email
    $user->markEmailAsVerified();

    // Nếu user đã đăng nhập, redirect về home, nếu chưa thì về login
    if (Auth::check() && Auth::id() == $user->id) {
        return redirect()->route('home')
            ->with('success', 'Email đã được xác thực thành công!');
    }

    return redirect()->route('login.form')
        ->with('success', 'Email đã được xác thực thành công! Bây giờ bạn có thể đăng nhập.');
})->middleware(['signed'])->name('verification.verify');

// Route để gửi lại email xác thực (nút "Gửi lại mail")
Route::post('/email/verification-notification', function (Request $request) {
    $user = $request->user();

    // Chỉ gửi nếu chưa verify
    if (!$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        // Reset flag để có thể gửi lại
        $request->session()->forget('verification_email_auto_sent');
        return back()->with('success', 'Đã gửi lại email xác thực. Vui lòng kiểm tra hộp thư.');
    }

    return back()->with('info', 'Email của bạn đã được xác thực rồi.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


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

    // Trang hiện form reset (có token)
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Nhận email để gửi link reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Submit mật khẩu mới
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

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
    ->middleware(['auth', 'isAdmin', 'verified'])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Quản lý thể loại
        Route::resource('categories', CategoryController::class);

        // Quản lý truyện
        Route::resource('comics', ComicController::class);

        // Quản lý thành viên
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

Route::prefix('user/profile')
    ->name('user.profile.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [UserProfileController::class, 'index'])->name('index');
        Route::get('/edit', [UserProfileController::class, 'editInfo'])->name('editInfo');
        Route::put('/edit', [UserProfileController::class, 'updateInfo'])->name('updateInfo');

        Route::get('/password', [UserProfileController::class, 'editPassword'])->name('editPassword');
        Route::put('/password', [UserProfileController::class, 'updatePassword'])->name('updatePassword');
    });
