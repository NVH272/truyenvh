<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\User;
use App\Models\CommentReport;
use App\Models\Chapter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Tổng Truyện
        $totalComics = Comic::count();

        // Tính % tăng trưởng so với tháng trước (Ví dụ đơn giản)
        $comicsLastMonth = Comic::where('created_at', '<', Carbon::now()->subMonth())->count();
        $comicsGrowth = $comicsLastMonth > 0 ? (($totalComics - $comicsLastMonth) / $comicsLastMonth) * 100 : 100;

        // 2. Tổng Lượt Xem
        $totalViews = Comic::sum('views');
        $viewsGrowth = 5.3; // Placeholder

        // 3. Tổng Thành viên
        $totalUsers = User::count();
        $usersLastMonth = User::where('created_at', '<', Carbon::now()->subMonth())->count();
        $usersGrowth = $usersLastMonth > 0 ? (($totalUsers - $usersLastMonth) / $usersLastMonth) * 100 : 0;

        // 4. Báo cáo lỗi (Pending)
        $pendingReports = DB::table('comment_reports')->where('status', 'pending')->count();

        // 5. Truyện cập nhật gần đây (Lấy 5 truyện mới nhất)
        $recentComics = Comic::with(['chapters' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 6. System Log (Tổng hợp từ các bảng)
        $logs = collect();

        // Log user đăng ký
        $newUsers = User::orderBy('created_at', 'desc')->take(3)->get()->map(function ($user) {
            return [
                'time' => $user->created_at,
                'message' => "New user <strong>{$user->name}</strong> registered.",
                'type' => 'user'
            ];
        });

        // Log truyện cập nhật (Dựa vào updated_at của truyện hoặc created_at của chapter)
        $updatedComics = Comic::orderBy('updated_at', 'desc')->take(3)->get()->map(function ($comic) {
            $updater = $comic->creator ? $comic->creator->name : 'Admin';
            return [
                'time' => $comic->updated_at,
                'message' => "Updated <span class='text-orange-400'>{$comic->title}</span>.",
                'type' => 'comic'
            ];
        });

        // Gộp và sort lại theo thời gian
        $systemLogs = $logs->merge($newUsers)->merge($updatedComics)->sortByDesc('time')->take(5);


        return view('admin.dashboard', compact(
            'totalComics',
            'comicsGrowth',
            'totalViews',
            'viewsGrowth',
            'totalUsers',
            'usersGrowth',
            'pendingReports',
            'recentComics',
            'systemLogs'
        ));
    }
}
