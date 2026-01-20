<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReadingHistory;
use App\Models\Comic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Sửa lại import DB chuẩn

class ReportController extends Controller
{
    public function index()
    {
        // 1. Views theo ngày (7 ngày gần nhất)
        $viewsByDay = ReadingHistory::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay()) // Thêm startOfDay để lấy trọn vẹn
            ->groupBy(DB::raw('DATE(created_at)')) // Sửa groupBy để tránh lỗi SQL Strict
            ->orderBy('date')
            ->get();

        // 2. Top truyện nhiều lượt đọc
        // Lưu ý: Đảm bảo Model Comic có function readingHistories() { return $this->hasMany(...); }
        $topComics = Comic::withCount('readingHistories')
            ->orderByDesc('reading_histories_count')
            ->limit(5)
            ->get();

        // 3. Người dùng mới
        $usersByDay = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.reports.index', compact(
            'viewsByDay',
            'topComics',
            'usersByDay'
        ));
    }
}
