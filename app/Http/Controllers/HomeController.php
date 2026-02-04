<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Category;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {

        // 1. TOP THỊNH HÀNH (Sắp xếp theo View giảm dần, lấy 10)
        $trendingComics = Comic::where('approval_status', 'approved')
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();

        // 2. MỚI CẬP NHẬT (Lấy 30 truyện để vừa đẹp lưới 5 cột x 6 hàng)
        $newUpdateComics = Comic::where('approval_status', 'approved') // Chỉ lấy truyện đã duyệt
            ->whereNotNull('last_chapter_at') // Bắt buộc phải có chương mới tính
            ->orderBy('last_chapter_at', 'desc') // Mới nhất lên đầu
            ->take(30) // Lấy 30 truyện (5 cột x 6 hàng)
            ->get();

        // 3. TOP THEO DÕI (Lấy 5 truyện) - chỉ truyện đã duyệt
        $topFollowComics = Comic::where('approval_status', 'approved')
            ->orderBy('follows', 'desc')
            ->take(5)
            ->get();

        // 4. TOP LƯỢT XEM (sidebar / section khác) - chỉ truyện đã duyệt
        $topViewedComics = Comic::query()
            ->where('approval_status', 'approved')
            ->withMax('chapters', 'chapter_number') // lấy chapter mới nhất
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        // Lấy 5 truyện view cao nhất cho slider
        $sliderComics = Comic::orderBy('views', 'desc')
            ->take(5)
            ->get();

        // JS của bạn đang cần các trường: img, title, desc, url, badge
        $sliderData = $sliderComics->map(function ($comic) {
            return [
                'img'   => $comic->cover_url, // Đường dẫn ảnh bìa
                'title' => $comic->title,
                'desc'  => \Str::limit($comic->description, 100), // Cắt ngắn mô tả
                'url'   => route('user.comics.show', $comic->slug), // Link truyện
                'badge' => $comic->status === 'ongoing' ? 'Hot' : 'Full', // Badge hiển thị
            ];
        });

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
            'genreSections' => $genreSections,
            'trendingComics' => $trendingComics,
            'newUpdateComics' => $newUpdateComics,
            'topFollowComics' => $topFollowComics,
            'topViewedComics' => $topViewedComics,
        ]);
    }
}
