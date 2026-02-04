<?php

namespace App\Http\Controllers\Comic;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Comment;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComicReadController extends Controller
{
    public function show(Request $request, Comic $comic)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'latest'); // latest | oldest | popular

        // Chỉ cho phép hiển thị truyện:
        // - Nếu truyện đã được duyệt (approval_status = approved)
        // - Hoặc user hiện tại chính là người đăng truyện (created_by)
        if ($comic->approval_status !== 'approved') {
            if (!$user || $user->id !== $comic->created_by) {
                // Ẩn truyện với người không có quyền xem
                abort(404);
            }
        }

        // Load sẵn quan hệ tác giả để dùng cho liên quan
        $comic->load('authors');

        $userRating  = null;
        $isFollowing = false;

        if ($user && $user->hasVerifiedEmail()) {
            $userRating = $comic->ratings()
                ->where('user_id', $user->id)
                ->value('rating');

            $isFollowing = $comic->followers()
                ->where('user_id', $user->id)
                ->exists();
        }

        $comic->load(['chapters' => function ($q) {
            $q->orderByDesc('chapter_number');
        }]);

        $commentsQuery = Comment::where('comic_id', $comic->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'comic:id,created_by',
                'replies' => function ($q) {
                    $q->with([
                        'user',
                        'comic:id,created_by',
                    ])
                        ->withCount([
                            'reactions as likes_count' => function ($q2) {
                                $q2->where('type', 'like');
                            },
                            'reactions as dislikes_count' => function ($q2) {
                                $q2->where('type', 'dislike');
                            },
                        ])
                        ->orderBy('created_at', 'asc');
                },
            ])
            ->withCount([
                'reactions as likes_count' => function ($q) {
                    $q->where('type', 'like');
                },
                'reactions as dislikes_count' => function ($q) {
                    $q->where('type', 'dislike');
                },
            ]);


        // Áp dụng bộ lọc sắp xếp
        switch ($filter) {
            case 'oldest':
                $commentsQuery->orderBy('created_at', 'asc');
                break;
            case 'popular':
                // Nhiều like nhất -> sort theo likes_count, rồi mới đến created_at mới nhất
                $commentsQuery->orderByDesc('likes_count')
                    ->orderByDesc('created_at');
                break;
            case 'latest':
            default:
                $commentsQuery->orderByDesc('created_at');
                break;
        }

        $comments = $commentsQuery->paginate(10)->appends(['filter' => $filter]);

        // Đếm tổng số comment và reply của truyện
        $totalCommentsCount = Comment::where('comic_id', $comic->id)->count();

        $limit = 5;

        // Lấy danh sách category id của truyện hiện tại
        $categoryIds = $comic->categories()->pluck('categories.id')->all(); // comic phải có relation categories()

        // 1) Ưu tiên: 5 truyện cùng ít nhất 1 tác giả (dựa trên quan hệ authors N-N)
        //    Sắp xếp: lượt xem giảm dần, rồi rating giảm dần
        $relatedComics = collect();

        if ($comic->authors->isNotEmpty()) {
            $authorIds = $comic->authors->pluck('id')->all();

            $relatedComics = Comic::query()
                ->where('approval_status', 'approved')
                ->where('id', '!=', $comic->id)
                ->whereHas('authors', function ($q) use ($authorIds) {
                    $q->whereIn('authors.id', $authorIds);
                })
                ->with(['categories', 'authors'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->orderByDesc('views')
                ->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating DESC')
                ->orderByDesc('ratings_count')
                ->limit($limit)
                ->get();
        }

        // 2) Fallback: 5 truyện cùng thể loại nếu không có cùng tác giả
        //    - Cùng càng nhiều thể loại thì đứng trên (matched_categories_count)
        //    - Sắp xếp tiếp theo: views, rating giảm dần
        if ($relatedComics->isEmpty() && !empty($categoryIds)) {
            $relatedComics = Comic::query()
                ->where('approval_status', 'approved')
                ->where('id', '!=', $comic->id)
                ->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                ->with(['categories', 'authors'])
                ->withCount([
                    'categories as matched_categories_count' => function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    },
                ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->orderByDesc('matched_categories_count')
                ->orderByDesc('views')
                ->orderByRaw('ratings_avg_rating IS NULL, ratings_avg_rating DESC')
                ->orderByDesc('ratings_count')
                ->limit($limit)
                ->get();
        }

        // Nếu request AJAX (dùng cho filter/pagination comments) thì trả về partial comments
        if ($request->ajax()) {
            return view('user.comics.partials.comments', [
                'comic'             => $comic,
                'comments'          => $comments,
                'commentFilter'     => $filter,
                'totalCommentsCount' => $totalCommentsCount,
            ]);
        }

        $firstChapter = Chapter::where('comic_id', $comic->id)
            ->orderBy('chapter_number')
            ->first();

        $latestChapter = Chapter::where('comic_id', $comic->id)
            ->orderByDesc('chapter_number')
            ->first();

        // Top lượt xem: chỉ tính truyện đã được duyệt
        $topViewedComics = Comic::query()
            ->where('approval_status', 'approved')
            ->withMax('chapters', 'chapter_number') // lấy chapter mới nhất
            ->orderByDesc('views')
            ->limit(5)
            ->get();


        $bannedWords = \App\Models\BannedWord::where('is_active', 1)->pluck('word')->toArray();

        $this->countComicView($comic);

        return view('user.comics.show', [
            'comic'             => $comic,
            'userRating'        => $userRating,
            'isFollowing'       => $isFollowing,
            'comments'          => $comments,
            'commentFilter'     => $filter,
            'relatedComics'     => $relatedComics,
            'totalCommentsCount' => $totalCommentsCount,
            'firstChapter'      => $firstChapter,
            'latestChapter'     => $latestChapter,
            'bannedWords' => $bannedWords,
            'topViewedComics'   => $topViewedComics,
        ]);
    }

    private function countComicView($comic)
    {
        $sessionKey = 'viewed_comic_' . $comic->id;

        if (!session()->has($sessionKey)) {
            $comic->increment('views');
            session()->put($sessionKey, true);
        }
    }
}
