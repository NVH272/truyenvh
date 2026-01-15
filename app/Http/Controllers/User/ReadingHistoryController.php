<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ReadingHistory;
use Illuminate\Http\Request;

class ReadingHistoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $chapter = Chapter::findOrFail($validated['chapter_id']);

        ReadingHistory::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'chapter_id' => $chapter->id,
            ],
            [
                'comic_id' => $chapter->comic_id,
                'progress' => $validated['progress'],
                'last_read_at' => now(),
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $histories = ReadingHistory::with([
            'comic:id,title,slug,cover_image',
            'chapter:id,chapter_number,title,comic_id'
        ])
            ->where('user_id', auth()->id())
            ->orderByDesc('last_read_at')
            ->get()
            ->groupBy('comic_id')
            ->map(function ($comicHistories) {
                // Sắp xếp các chương trong mỗi truyện theo thời gian đọc mới nhất
                return $comicHistories->sortByDesc('last_read_at')->values();
            })
            ->sortByDesc(function ($comicHistories) {
                // Sắp xếp các truyện theo thời gian đọc mới nhất
                return $comicHistories->max('last_read_at');
            })
            ->values();

        return view('user.reading_history.index', compact('histories'));
    }
}
