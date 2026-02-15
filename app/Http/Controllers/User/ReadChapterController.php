<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Comic\ComicCommentController;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\ReadingHistory;
use App\Models\Comment;

class ReadChapterController extends Controller
{
    public function show(Request $request, $comic, $chapter_number)
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

        $comments = Comment::with(['user', 'replies.user', 'replies.reactions']) // Eager load user và replies
            ->where('comic_id', $comic->id)
            ->where('chapter_id', $chapter->id)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->paginate(10);

        $filter = $request->input('filter', 'latest'); // latest | oldest | popular

        // 1. Tạo Query Builder
        $commentsQuery = Comment::where('chapter_id', $chapter->id) // Quan trọng: Chỉ lấy của chapter này
            ->whereNull('parent_id')
            ->with([
                'user',
                'chapter', // Load chapter để hiển thị badge (như yêu cầu trước)
                'replies' => function ($q) {
                    $q->with(['user', 'chapter'])
                        ->withCount([
                            'reactions as likes_count' => fn($q2) => $q2->where('type', 'like'),
                            'reactions as dislikes_count' => fn($q2) => $q2->where('type', 'dislike'),
                        ])
                        ->orderBy('created_at', 'asc');
                },
            ])
            ->withCount([
                'reactions as likes_count' => fn($q) => $q->where('type', 'like'),
                'reactions as dislikes_count' => fn($q) => $q->where('type', 'dislike'),
            ]);

        // 2. Áp dụng bộ lọc
        switch ($filter) {
            case 'oldest':
                $commentsQuery->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $commentsQuery->orderByDesc('likes_count')
                    ->orderByDesc('created_at');
                break;
            case 'latest':
            default:
                $commentsQuery->orderByDesc('created_at');
                break;
        }

        // 3. Phân trang
        $comments = $commentsQuery->paginate(10)->appends(['filter' => $filter]);

        // 4. Đếm tổng số bình luận (bao gồm cả reply) trong chapter này
        $totalCommentsCount = Comment::where('chapter_id', $chapter->id)->count();

        // 5. Xử lý AJAX (Khi người dùng chọn dropdown filter)
        if ($request->ajax()) {
            return view('user.comics.partials.comments.index', [
                'comic'              => $comic,
                'chapter'            => $chapter,
                'comments'           => $comments,
                'commentFilter'      => $filter,
                'totalCommentsCount' => $totalCommentsCount,
                'theme'              => 'dark'
            ]);
        }

        return view('user.comics.chapters.read', compact('comic', 'chapter', 'prevChapter', 'nextChapter', 'firstChapter', 'latestChapter', 'currentProgress', 'comments', 'totalCommentsCount', 'filter'));
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
