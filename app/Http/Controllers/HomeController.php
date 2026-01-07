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
        // Dùng đúng slug trong bảng `categories` (xem file dump db_truyenvh.sql)
        // Ví dụ: action, adventure, comedy, drama, fantasy, romance, horror, sports, ...
        $genreSlugs = [
            'action'      => 'Truyện hành động',
            'adventure'   => 'Truyện phiêu lưu',
            'comedy'      => 'Truyện hài hước',
            'drama'       => 'Truyện kịch tính',
            'fantasy'     => 'Truyện giả tưởng',
            'romance'     => 'Truyện tình cảm',
            'horror'      => 'Truyện kinh dị',
            'sports'      => 'Truyện thể thao',
            'school-life' => 'Đời sống học đường',
            'sci-fi'      => 'Khoa học viễn tưởng',
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
                'url'   => route('user.comics.show', $comic->slug),
                'slug'  => $comic->slug,
            ];
        })->values();

        $trendingData = $trendingComics->map(function ($comic) {
            return [
                'id'    => $comic->id,
                'title' => $comic->title,
                'chap'  => $comic->chapter_count ?? 0,
                'view'  => $comic->views ?? 0,
                'img'   => $comic->cover_url,
                'url'   => route('user.comics.show', $comic->slug),
                'slug'  => $comic->slug,
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
                'url'   => route('user.comics.show', $comic->slug),
                'slug'  => $comic->slug,
            ];
        })->values();

        $sidebarData = $topFollow->map(function ($comic) {
            return [
                'title' => $comic->title,
                'chap'  => $comic->chapter_count ?? 0,
                'view'  => $comic->follows ?? 0,
                'img'   => $comic->cover_url,
                'url'   => route('user.comics.show', $comic->slug),
                'slug'  => $comic->slug,
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
