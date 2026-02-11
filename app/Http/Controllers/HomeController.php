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
            ->take(20)
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
        $sliderComics = Comic::where('approval_status', 'approved')
            ->with('categories') // Eager load thể loại
            ->orderByDesc('views')
            ->take(15)
            ->get();

        $sliderData = $sliderComics->map(function ($comic, $key) {
            return [
                'id'          => $comic->id,
                'title'       => $comic->title,
                'desc'        => Str::limit($comic->description ?? 'Chưa có mô tả...', 150),
                'img'         => $comic->cover_url,
                'author'      => $comic->author ?? 'Đang cập nhật',
                'views'       => number_format($comic->views ?? 0),
                // Lấy 3 thể loại đầu tiên
                'genres'      => $comic->categories->take(3)->map(fn($c) => $c->name)->toArray(),
                'url'         => route('user.comics.show', $comic->slug),
                'badge'       => 'Top ' . ($key + 1 ?? 1), // Ví dụ: Top 1, Top 2
            ];
        })->values();

        // Chỉ lấy truyện đã được duyệt
        $baseQuery = Comic::where('approval_status', 'approved');

        

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
            'action'      => 'Truyện Action',
            'adventure'   => 'Truyện Adventure',
            'comedy'      => 'Truyện Comedy',
            'drama'       => 'Truyện Drama',
            'fantasy'     => 'Truyện Fantasy',
            'romance'     => 'Truyện Romance',
            'horror'      => 'Truyện Horror',
            'sports'      => 'Truyện Sports',
            'school-life' => 'Truyện School Life',
            'sci-fi'      => 'Truyện Sci-Fi',
        ];

        $genreSections = [];

        foreach ($genreSlugs as $slug => $label) {
            $category = Category::where('slug', $slug)->first();

            $comics = $category
                ? $category->comics()
                ->where('approval_status', 'approved')
                // Ưu tiên truyện có chapter mới cập nhật (last_chapter_at mới nhất)
                ->orderByDesc('last_chapter_at')
                ->orderByDesc('updated_at')
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
