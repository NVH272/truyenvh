<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Comment;
use App\Models\ReadingHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        // --- KPI TỔNG QUAN ---
        $totalComics = Comic::count();
        $totalChapters = Chapter::count();
        $totalUsers = User::count();
        $totalViews = (int) Comic::sum('views');
        $totalFollows = (int) Comic::sum('follows');
        $totalComments = Comment::where('is_deleted', false)->count();

        // --- NHÃN 7 NGÀY (để fill ngày thiếu = 0) ---
        $chartLabels = [];
        $viewsByDayMap = [];
        $usersByDayMap = [];
        $commentsByDayMap = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $viewsByDayMap[$d] = 0;
            $usersByDayMap[$d] = 0;
            $commentsByDayMap[$d] = 0;
        }

        // 1. Lượt đọc theo ngày (ReadingHistory - phiên đọc)
        $viewsByDay = ReadingHistory::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        foreach ($viewsByDay as $r) {
            $viewsByDayMap[(string) $r->date] = (int) $r->total;
        }
        $viewsByDayData = array_values($viewsByDayMap);

        // 2. Người dùng mới theo ngày
        $usersByDay = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        foreach ($usersByDay as $r) {
            $usersByDayMap[(string) $r->date] = (int) $r->total;
        }
        $usersByDayData = array_values($usersByDayMap);

        // 3. Bình luận mới theo ngày (bỏ đã xóa)
        $commentsByDay = Comment::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('is_deleted', false)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        foreach ($commentsByDay as $r) {
            $commentsByDayMap[(string) $r->date] = (int) $r->total;
        }
        $commentsByDayData = array_values($commentsByDayMap);

        // 4. Top truyện theo lượt đọc (phiên đọc - readingHistories)
        $topComics = Comic::withCount('readingHistories')
            ->orderByDesc('reading_histories_count')
            ->limit(5)
            ->get();
        $topComicsTitles = $topComics->map(fn ($c) => Str::limit($c->title, 28))->values();

        // 5. Top truyện theo lượt xem (cột views)
        $topComicsByViews = Comic::orderByDesc('views')->limit(5)->get();
        $topComicsByViewsTitles = $topComicsByViews->map(fn ($c) => Str::limit($c->title, 28))->values();

        // 6. Top truyện theo theo dõi (cột follows)
        $topComicsByFollows = Comic::orderByDesc('follows')->limit(5)->get();
        $topComicsByFollowsTitles = $topComicsByFollows->map(fn ($c) => Str::limit($c->title, 28))->values();

        // 7. Truyện theo trạng thái (ongoing, completed, dropped)
        $comicsByStatusRaw = Comic::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $statusLabels = ['ongoing' => 'Đang tiến hành', 'completed' => 'Hoàn thành', 'dropped' => 'Bỏ dở'];
        $comicsByStatusLabels = [];
        $comicsByStatusData = [];
        foreach (['ongoing', 'completed', 'dropped'] as $s) {
            $comicsByStatusLabels[] = $statusLabels[$s];
            $comicsByStatusData[] = (int) ($comicsByStatusRaw->get($s)->total ?? 0);
        }

        return view('admin.reports.index', compact(
            'totalComics',
            'totalChapters',
            'totalUsers',
            'totalViews',
            'totalFollows',
            'totalComments',
            'chartLabels',
            'viewsByDayData',
            'usersByDayData',
            'commentsByDayData',
            'topComics',
            'topComicsTitles',
            'topComicsByViews',
            'topComicsByViewsTitles',
            'topComicsByFollows',
            'topComicsByFollowsTitles',
            'comicsByStatusLabels',
            'comicsByStatusData'
        ));
    }
}
