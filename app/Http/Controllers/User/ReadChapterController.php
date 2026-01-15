<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Comic\ComicCommentController;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\ReadingHistory;

class ReadChapterController extends Controller
{
    public function show($comic, $chapter_number)
    {


        $comic = Comic::findOrFail($comic);

        $chapterNumber = (int) $chapter_number;

        $chapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', $chapterNumber)
            ->with(['pages' => fn($q) => $q->orderBy('page_index')])
            ->firstOrFail();

        // Chapter trước: số nhỏ hơn gần nhất
        $prevChapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', '<', $chapterNumber)
            ->orderByDesc('chapter_number')
            ->first();

        // Chapter sau: số lớn hơn gần nhất
        $nextChapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', '>', $chapterNumber)
            ->orderBy('chapter_number')
            ->first();

        // Chapter đầu tiên
        $firstChapter = Chapter::where('comic_id', $comic->id)
            ->orderBy('chapter_number')
            ->first();

        // Chapter mới nhất
        $latestChapter = Chapter::where('comic_id', $comic->id)
            ->orderByDesc('chapter_number')
            ->first();

        $this->countChapterView($chapter);

        // Lấy progress hiện tại của chapter này (nếu có)
        $currentProgress = 0;
        if (auth()->check()) {
            $history = ReadingHistory::where('user_id', auth()->id())
                ->where('chapter_id', $chapter->id)
                ->first();
            if ($history) {
                $currentProgress = $history->progress;
            }
        }

        return view('user.comics.chapters.read', compact('comic', 'chapter', 'prevChapter', 'nextChapter', 'firstChapter', 'latestChapter', 'currentProgress'));
    }

    private function countChapterView($chapter)
    {
        $sessionKey = 'viewed_chapter_' . $chapter->id;

        if (!session()->has($sessionKey)) {
            $chapter->increment('views');
            session()->put($sessionKey, true);
        }
    }

    protected function saveReadingHistory($chapter, int $progress)
    {
        if (!auth()->check()) {
            return;
        }

        ReadingHistory::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'chapter_id' => $chapter->id,
            ],
            [
                'comic_id' => $chapter->comic_id,
                'progress' => $progress,
                'last_read_at' => now(),
            ]
        );
    }
}
