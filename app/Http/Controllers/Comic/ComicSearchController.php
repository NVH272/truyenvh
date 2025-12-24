<?php

namespace App\Http\Controllers\Comic;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\Http\Request;
use App\Models\Category;

class ComicSearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $comics = Comic::query()
            ->where('approval_status', 'approved')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%");
                });
            })
            ->with('categories')
            ->orderByDesc('updated_at')
            ->paginate(24)
            ->withQueryString();

        return view('user.comics.filter.filter', compact('q', 'comics', 'categories'));
    }
}
