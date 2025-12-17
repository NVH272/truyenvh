<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserComicController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\ComicReadController;
use App\Http\Controllers\User\ComicInteractionController;
use App\Http\Controllers\User\ComicFollowController;
use App\Http\Controllers\User\ComicSearchController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang chi tiết / đọc truyện theo slug
Route::get('/truyen/{comic:slug}', [ComicReadController::class, 'show'])
    ->name('user.comics.show');

// Các route tương tác truyện (theo dõi, đánh giá) - chỉ cho user đã xác thực email
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/comics/{comic}/follow', [ComicInteractionController::class, 'toggleFollow'])
        ->name('comics.follow');

    Route::post('/comics/{comic}/rate', [ComicInteractionController::class, 'rate'])
        ->name('comics.rate');
});

// Trang tìm kiếm truyện
Route::get('/search', [ComicSearchController::class, 'index'])
    ->name('user.comics.search');

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'throttle:6,1'])->group(function () {
    Route::get('/email/verify', function (Request $request) {
        $user = $request->user();
        if (!$user->hasVerifiedEmail()) {
            if (!$request->session()->has('verification_email_auto_sent')) {
                $user->sendEmailVerificationNotification();
                $request->session()->put('verification_email_auto_sent', true);
                return view('auth.verify-email')->with('info', 'Email xác thực đã được gửi tự động đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư.');
            }
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            $request->session()->forget('verification_email_auto_sent');
            return back()->with('success', 'Đã gửi lại email xác thực. Vui lòng kiểm tra hộp thư.');
        }
        return back()->with('info', 'Email của bạn đã được xác thực rồi.');
    })->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Liên kết xác thực không hợp lệ.');
    }
    if (!URL::hasValidSignature($request)) {
        abort(403, 'Liên kết xác thực đã hết hạn hoặc không hợp lệ.');
    }
    if ($user->hasVerifiedEmail()) {
        if (Auth::check() && Auth::id() == $user->id) {
            return redirect()->route('home')->with('info', 'Email của bạn đã được xác thực trước đó.');
        }
        return redirect()->route('login.form')->with('info', 'Email của bạn đã được xác thực trước đó. Bạn có thể đăng nhập.');
    }
    $user->markEmailAsVerified();
    if (Auth::check() && Auth::id() == $user->id) {
        return redirect()->route('home')->with('success', 'Email đã được xác thực thành công!');
    }
    return redirect()->route('login.form')->with('success', 'Email đã được xác thực thành công! Bây giờ bạn có thể đăng nhập.');
})->middleware(['signed'])->name('verification.verify');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegisterForm')->name('register.form');
    Route::post('/register', 'register')->name('register');
    Route::get('/login', 'showLoginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');
    Route::get('/logout', 'logout')->name('logout.get');
    Route::post('/logout', 'logout')->name('logout');
});

Route::prefix('forgot-password')->name('password.')->group(function () {
    Route::get('/', [PasswordResetLinkController::class, 'create'])->name('request');
    Route::post('/', [PasswordResetLinkController::class, 'store'])->name('email');
});

Route::prefix('reset-password')->name('password.')->group(function () {
    Route::get('/{token}', [NewPasswordController::class, 'create'])->name('reset');
    Route::post('/', [NewPasswordController::class, 'store'])->name('update');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Trang liệt kê truyện đang theo dõi
    Route::get('/followed', [ComicFollowController::class, 'index'])
        ->name('user.comics.followed');
});

/*
|--------------------------------------------------------------------------
| USER PROFILE ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('user/profile')->name('user.profile.')->middleware('auth')->group(function () {
    Route::get('/', [UserProfileController::class, 'index'])->name('index');
    Route::get('/edit', [UserProfileController::class, 'editInfo'])->name('editInfo');
    Route::put('/edit', [UserProfileController::class, 'updateInfo'])->name('updateInfo');
    Route::get('/password', [UserProfileController::class, 'editPassword'])->name('editPassword');
    Route::put('/password', [UserProfileController::class, 'updatePassword'])->name('updatePassword');
});

/*
|--------------------------------------------------------------------------
| USER COMICS ROUTES (Admin + Poster)
|--------------------------------------------------------------------------
*/

Route::prefix('my-comics')->name('user.my-comics.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [UserComicController::class, 'index'])->name('index');
    Route::get('/create', [UserComicController::class, 'create'])->name('create');
    Route::post('/', [UserComicController::class, 'store'])->name('store');
    Route::get('/{comic}/edit', [UserComicController::class, 'edit'])->name('edit');
    Route::put('/{comic}', [UserComicController::class, 'update'])->name('update');
    Route::delete('/{comic}', [UserComicController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'isAdmin', 'verified'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);

    // Routes cho comics - đặt trước resource để tránh conflict với {comic} parameter
    Route::get('comics/pending', [ComicController::class, 'pending'])->name('comics.pending');
    Route::post('comics/{comic}/approve', [ComicController::class, 'approve'])->name('comics.approve');
    Route::post('comics/{comic}/reject', [ComicController::class, 'reject'])->name('comics.reject');

    // Resource route cho comics (phải đặt sau các route đặc biệt để tránh conflict)
    Route::resource('comics', ComicController::class)->except(['show']);

    // Lịch sử duyệt truyện (đã duyệt + đã từ chối)
    Route::get('/comics/review-history', [ComicController::class, 'reviewHistory'])
        ->name('comics.review_history');

    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});
