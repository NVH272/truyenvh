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

        if (!empty($selected)) {
            $comicsQuery->whereHas('categories', function ($q) use ($selected) {
                $q->whereIn('categories.slug', $selected);
            });
        }

        // (tuỳ chọn) sort
        $sort = $request->input('sort', 'new'); // new | old | popular
        if ($sort === 'old') {
            $comicsQuery->orderBy('created_at', 'asc');
        } elseif ($sort === 'popular') {
            $comicsQuery->orderByDesc('views');
        } else {
            $comicsQuery->orderByDesc('created_at');
        }

        $comics = $comicsQuery->paginate(20)->withQueryString();

        return view('user.comics.filter.filter', compact(
            'categories',
            'comics',
            'selected',
            'sort'
        ));
    }
}
