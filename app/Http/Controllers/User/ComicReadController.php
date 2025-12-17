<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ComicReadController extends Controller
{
    public function show(Comic $comic)
    {
        $user = Auth::user();

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

        $comments = Comment::where('comic_id', $comic->id)
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
                        ]);
                },
            ])
            ->withCount([
                'reactions as likes_count' => function ($q) {
                    $q->where('type', 'like');
                },
                'reactions as dislikes_count' => function ($q) {
                    $q->where('type', 'dislike');
                },
            ])
            ->latest()
            ->paginate(10);

        return view('user.comics.index', [
            'comic'       => $comic,
            'userRating'  => $userRating,
            'isFollowing' => $isFollowing,
            'comments'    => $comments,
        ]);
    }
}
