<?php

namespace App\Http\Controllers\Poster;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\Http\Request;

class MyComicsController extends Controller
{
    // Trang 1: danh sách truyện của người đăng
    public function index(Request $request)
    {
        $userId = auth()->id();

        $q = Comic::query()
            ->where('created_by', $userId); // ✅ ĐÚNG CỘT

        if ($search = $request->get('q')) {
            $q->where('title', 'like', "%{$search}%");
        }

        $comics = $q->withCount('chapters')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('poster.index', compact('comics'));
    }

    // Trang 2: danh sách chapter của truyện được click
    public function chapters(Comic $comic)
    {
        $userId = auth()->id();

        // Chặn truy cập truyện không thuộc uploader hiện tại
        abort_unless((int)$comic->created_by === (int)$userId, 403);

        $chapters = $comic->chapters()
            ->orderByRaw('CAST(chapter_number AS DECIMAL(10,2)) DESC')
            // ^ nếu chapter_number là string và có dạng 6.5, 7.5
            ->paginate(20);

        return view('poster.chapters.index', compact('comic', 'chapters'));
    }
}
