<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComicReadController extends Controller
{
    public function show(Request $request, Comic $comic)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'latest'); // latest | oldest | popular

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

        $commentsQuery = Comment::where('comic_id', $comic->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies' => function ($q) {
                    $q->with('user')
                        ->withCount([
                            'reactions as likes_count' => function ($q2) {
                                $q2->where('type', 'like');
                            },
                            'reactions as dislikes_count' => function ($q2) {
                                $q2->where('type', 'dislike');
                            },
                        ])
                        ->orderBy('created_at', 'asc'); // replies: cũ -> mới
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

        // Nếu request AJAX (dùng cho filter/pagination comments) thì trả về partial comments
        if ($request->ajax()) {
            return view('user.comics.partials.comments', [
                'comic'         => $comic,
                'comments'      => $comments,
                'commentFilter' => $filter,
            ]);
        }

        return view('user.comics.index', [
            'comic'         => $comic,
            'userRating'    => $userRating,
            'isFollowing'   => $isFollowing,
            'comments'      => $comments,
            'commentFilter' => $filter,
        ]);
    }
}
