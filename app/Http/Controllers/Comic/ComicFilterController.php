<?php

namespace App\Http\Controllers\Comic;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comic;
use Illuminate\Http\Request;

class ComicFilterController extends Controller
{
    public function index(Request $request)
    {
        // 1) Danh sách thể loại (render tags)
        $categories = Category::where('is_active', 1)
            ->orderBy('name')
            ->get();

        // 2) Các slug đang được chọn (multi)
        $selected = $request->input('categories', []); // categories[]=slug
        $selected = is_array($selected) ? array_values(array_filter($selected)) : [];

        // 3) Query truyện
        $comicsQuery = Comic::query()
            ->where('approval_status', 'approved')
            ->with('categories'); // nếu cần show thể loại

        $comicsQuery->withAvg('ratings', 'rating')
            ->withCount('ratings');


        if (!empty($selected)) {
            $comicsQuery->whereHas('categories', function ($q) use ($selected) {
                $q->whereIn('categories.slug', $selected);
            });
        }

        $sort = $request->input('sort', 'rating_desc');

        switch ($sort) {
            case 'views_desc':
                $comicsQuery->orderByDesc('views');
                break;

            case 'views_asc':
                $comicsQuery->orderBy('views');
                break;

            case 'rating_desc':
                // NULL (chưa có rating) sẽ xuống cuối
                $comicsQuery->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating DESC');
                break;

            case 'rating_asc':
                // NULL xuống cuối
                $comicsQuery->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating ASC');
                break;

            case 'chapters_desc':
                $comicsQuery->orderByDesc('chapter_count');
                break;

            case 'chapters_asc':
                $comicsQuery->orderBy('chapter_count');
                break;

            default:
                $comicsQuery->orderByDesc('updated_at');
                break;
        }

        $comics = $comicsQuery->paginate(20)->withQueryString();

        return view('user.comics.filter.filter', compact(
            'categories',
            'comics',
            'selected',
            'sort'
        ));
    }

    public function search(Request $request)
    {
        $query = Comic::query()
            ->where('approval_status', 'approved')
            ->with('categories')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            });
        }

        $selected = $request->input('categories', []);
        $selected = is_array($selected) ? array_values(array_filter($selected)) : [];
        if (!empty($selected)) {
            $query->whereHas('categories', function ($qq) use ($selected) {
                $qq->whereIn('categories.slug', $selected);
            });
        }

        $sort = $request->input('sort', 'rating_desc');
        switch ($sort) {
            case 'views_desc':
                $query->orderByDesc('views');
                break;

            case 'views_asc':
                $query->orderBy('views');
                break;

            case 'rating_desc':
                $query->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating DESC');
                break;

            case 'rating_asc':
                $query->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating ASC');
                break;

            case 'chapters_desc':
                $query->orderByDesc('chapter_count');
                break;

            case 'chapters_asc':
                $query->orderBy('chapter_count');
                break;

            default:
                $query->orderByDesc('updated_at');
                break;
        }

        $comics = $query->paginate(50)->withQueryString();

        return view('user.search', compact('comics', 'q', 'selected', 'sort'));
    }
}
