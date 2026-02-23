<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Category;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {

        // 1. TOP THỊNH HÀNH
        $trendingComics = Comic::where('approval_status', 'approved')
            ->orderBy('views', 'desc')
            ->take(20)
            ->get();

        // 2. MỚI CẬP NHẬT
        $newUpdateComics = Comic::where('approval_status', 'approved')
            ->whereNotNull('last_chapter_at') // Bắt buộc phải có chương mới tính
            ->orderBy('last_chapter_at', 'desc')
            ->take(15)
            ->get();

        // 3. TOP THEO DÕI
        $topFollowComics = Comic::where('approval_status', 'approved')
            ->orderBy('follows', 'desc')
            ->take(5)
            ->get();

        // 4. TOP LƯỢT XEM
        $topViewedComics = Comic::query()
            ->where('approval_status', 'approved')
            ->withMax('chapters', 'chapter_number') // lấy chapter mới nhất
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        // Lấy truyện view cao nhất cho slider
        $sliderComics = Comic::where('approval_status', 'approved')
            ->with('categories')
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

        // 3. Các section theo thể loại (lọc qua bảng categories + category_comic)
        // Dùng đúng slug trong bảng `categories` (xem file dump db_truyenvh.sql)
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
                // Ưu tiên truyện có chapter mới cập nhật
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

        // Chuẩn hoá cho JS slider / carousel

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

        return view('home', [
            'sliderData'    => $sliderData,
            'genreSections' => $genreSections,
            'trendingComics' => $trendingComics,
            'newUpdateComics' => $newUpdateComics,
            'topFollowComics' => $topFollowComics,
            'topViewedComics' => $topViewedComics,
        ]);
    }
}
