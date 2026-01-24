<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ViolationController;
use App\Http\Controllers\Admin\BannedWordController;
use App\Http\Controllers\Admin\AdminChapterController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserComicController;
use App\Http\Controllers\User\ChapterController;
use App\Http\Controllers\User\CommentReportController;
use App\Http\Controllers\User\ReadChapterController;
use App\Http\Controllers\User\ReadingHistoryController;

use App\Http\Controllers\Poster\MyComicsController;

use App\Http\Controllers\Comic\ComicReadController;
use App\Http\Controllers\Comic\ComicInteractionController;
use App\Http\Controllers\Comic\ComicFollowController;
use App\Http\Controllers\Comic\ComicSearchController;
use App\Http\Controllers\Comic\ComicCommentController;
use App\Http\Controllers\Comic\ComicFilterController;
use App\Http\Controllers\Comic\ComicAuthorController;


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

// Trang đọc chapter
Route::get('/comics/{comic}/chapter-{chapter_number}', [ReadChapterController::class, 'show'])
    ->whereNumber('comic')
    ->name('user.comics.chapters.read');

// Trang tìm kiếm truyện
Route::get('/search', [ComicSearchController::class, 'index'])
    ->name('user.comics.search');

// Trang lọc truyện
Route::get('/comics/filter', [ComicFilterController::class, 'index'])
    ->name('user.comics.filter');

// Trang tác giả
Route::get('/author/{author}', [ComicAuthorController::class, 'show'])
    ->name('user.comics.author.show');

// Nhóm các trang thông tin (Policies & Info)
Route::controller(PolicyController::class)->group(function () {
    Route::get('/lien-he', 'contact')->name('contact');
    Route::get('/ve-chung-toi', 'about')->name('about');
    Route::get('/dieu-khoan-dich-vu', 'terms')->name('terms');
    Route::get('/chinh-sach-bao-mat', 'privacy')->name('privacy');
    Route::get('/mien-tru-trach-nhiem', 'disclaimer')->name('disclaimer');
});

/*
|--------------------------------------------------------------------------
| LOGGED ROUTES
|--------------------------------------------------------------------------
*/


// Theo dõi & đánh giá: yêu cầu user đã xác thực email
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/comics/{comic}/follow', [ComicInteractionController::class, 'toggleFollow'])
        ->name('comics.follow');

    Route::post('/comics/{comic}/rate', [ComicInteractionController::class, 'rate'])
        ->name('comics.rate');

    Route::post('/reading-history', [ReadingHistoryController::class, 'store'])
        ->middleware('auth')
        ->name('reading-history.store');
    Route::get('/reading-history', [ReadingHistoryController::class, 'index'])
        ->middleware('auth')
        ->name('user.reading-history.index');
});

// Bình luận & reaction: chỉ cần đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::post('/comics/{comic}/comments', [ComicCommentController::class, 'store'])
        ->name('comments.store');

    Route::post('/comments/{comment}/reaction/{type}', [ComicCommentController::class, 'toggleReaction'])
        ->whereIn('type', ['like', 'dislike'])
        ->name('comments.reaction');
    Route::delete('/comments/{comment}', [ComicCommentController::class, 'destroy'])
        ->name('comments.destroy')
        ->middleware('auth');
    Route::post('/comments/{comment}/report', [CommentReportController::class, 'store'])
        ->name('comments.report');
});

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
| CHAPTER ROUTES (Comic Owner Only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('comics/{comic}/chapters')->name('user.comics.chapters.')->group(function () {
        Route::get('/create', [ChapterController::class, 'create'])->name('create');
        Route::post('/', [ChapterController::class, 'store'])->name('store');
        Route::get('/{chapter}/edit', [ChapterController::class, 'edit'])->name('edit');
        Route::put('/{chapter}', [ChapterController::class, 'update'])->name('update');
        Route::delete('/{chapter}', [ChapterController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'verified'])
    ->prefix('poster')
    ->name('poster.')
    ->group(function () {
        Route::get('/comics', [MyComicsController::class, 'index'])->name('index');
        Route::get('/comics/{comic:slug}/chapters', [MyComicsController::class, 'chapters'])->name('chapters');
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

    // Lịch sử duyệt truyện
    Route::get('/comics/review-history', [ComicController::class, 'reviewHistory'])
        ->name('comics.review_history');

    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::get('violation', [ViolationController::class, 'index'])->name('violation.index');

    Route::get('/banned-words', [BannedWordController::class, 'index'])->name('banned_words.index');
    Route::post('/banned-words', [BannedWordController::class, 'store'])->name('banned_words.store');
    Route::put('/banned-words/{bannedWord}', [BannedWordController::class, 'update'])->name('banned_words.update');
    Route::delete('/banned-words/{bannedWord}', [BannedWordController::class, 'destroy'])->name('banned_words.destroy');

    Route::get('/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
    Route::get('chapters/{comic}', [AdminChapterController::class, 'index'])->name('chapters.by-comic');
    Route::delete('/comics/{comic}/chapters/{chapter}', [AdminChapterController::class, 'destroy'])->name('chapters.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Route::post('/reading-history', [ReadingHistoryController::class, 'store'])
//     ->middleware('auth')
//     ->name('reading-history.store');
// Route::get('/reading-history', [ReadingHistoryController::class, 'index'])
//     ->middleware('auth')
//     ->name('user.reading-history.index');
