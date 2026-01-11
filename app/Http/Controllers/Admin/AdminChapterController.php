<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Chapter;
use Illuminate\Http\Request;

class AdminChapterController extends Controller
{
    public function index(Request $request, Comic $comic = null)
    {
        // Danh sách tất cả truyện (cho dropdown)
        $comics = Comic::orderBy('title')->get();

        $chapters = null;

        // Nếu có comic → load chapter
        if ($comic) {
            $chapters = $comic->chapters()
                ->orderByRaw('CAST(chapter_number AS DECIMAL(10,2)) DESC')
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.chapters.index', compact(
            'comics',
            'comic',
            'chapters'
        ));
    }

    public function destroy(Comic $comic, Chapter $chapter)
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $chapter->delete();

        return redirect()
            ->back()
            ->with('success', 'Chapter đã được xóa');
    }
}
