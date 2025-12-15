<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Category;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        // Chỉ lấy truyện đã được duyệt
        $baseQuery = Comic::where('approval_status', 'approved');

        // 1. Slider: top view
        $sliderComics = (clone $baseQuery)
            ->orderByDesc('views')
            ->take(3)
            ->get();

        // 2. Top Thịnh Hành: theo follows
        $trendingComics = (clone $baseQuery)
            ->orderByDesc('follows')
            ->take(10)
            ->get();

        // 3. Mới cập nhật
        $recentUpdates = (clone $baseQuery)
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        // 4. Top theo dõi (sidebar)
        $topFollow = (clone $baseQuery)
            ->orderByDesc('follows')
            ->take(5)
            ->get();

        // 5. Các section theo thể loại (lọc qua bảng categories + category_comic)
        // slug trong bảng categories: hanh-dong, ngon-tinh, co-trang, the-thao, ...
        $genreSlugs = [
            'hanh-dong' => 'Truyện hành động',
            'ngon-tinh' => 'Truyện ngôn tình',
            'co-trang'  => 'Truyện cổ trang',
            'the-thao'  => 'Truyện thể thao',
        ];

        $genreSections = [];

        foreach ($genreSlugs as $slug => $label) {
            $category = Category::where('slug', $slug)->first();

            $comics = $category
                ? $category->comics()
                ->where('approval_status', 'approved')
                ->latest('updated_at')
                ->take(12)
                ->get()
                : collect();

            $genreSections[] = [
                'slug'   => $slug,
                'label'  => $label,
                'comics' => $comics,
            ];
        }

        // ===== Chuẩn hoá cho JS slider / carousel =====

        $sliderData = $sliderComics->map(function ($comic) {
            return [
                'title' => $comic->title,
                'desc'  => Str::limit($comic->description ?? '', 120),
                'img'   => $comic->cover_url,
                'badge' => 'Hot Nhất',
                'url'   => '#', // sau này thay route đọc truyện
            ];
        })->values();

        $trendingData = $trendingComics->map(function ($comic) {
            return [
                'id'    => $comic->id,
                'title' => $comic->title,
                'chap'  => $comic->chapter_count ?? 0,
                'view'  => $comic->views ?? 0,
                'img'   => $comic->cover_url,
                'url'   => '#',
            ];
        })->values();

        $updatesData = $recentUpdates->map(function ($comic) {
            return [
                'title' => $comic->title,
                'img'   => $comic->cover_url,
                'isHot' => ($comic->views ?? 0) > 10000,
                'chaps' => [
                    [
                        'num'  => $comic->chapter_count ?? 1,
                        'time' => optional($comic->updated_at)->diffForHumans() ?? 'Mới cập nhật',
                    ],
                ],
                'url'   => '#',
            ];
        })->values();

        $sidebarData = $topFollow->map(function ($comic) {
            return [
                'title' => $comic->title,
                'chap'  => $comic->chapter_count ?? 0,
                'view'  => $comic->follows ?? 0,
                'img'   => $comic->cover_url,
                'url'   => '#',
            ];
        })->values();

        return view('home', [
            'sliderData'    => $sliderData,
            'trendingData'  => $trendingData,
            'updatesData'   => $updatesData,
            'sidebarData'   => $sidebarData,
            'genreSections' => $genreSections, // dùng cho các hàng “Truyện HangTruyen / Gợi ý / Tuyển chọn...”
        ]);
    }
}
