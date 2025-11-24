<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra đã đăng nhập và có role = 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Không phải admin → tuỳ bạn redirect hoặc abort
        // return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}
