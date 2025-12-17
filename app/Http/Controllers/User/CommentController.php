<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\Comment;

class ComicCommentController extends Controller
{
    public function store(Request $request, Comic $comic)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'comic_id'  => $comic->id,
            'user_id'   => auth()->id(),
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        if ($request->parent_id) {
            Comment::where('id', $request->parent_id)->increment('replies_count');
        }

        return back();
    }

    public function toggleLike(Comment $comment)
    {
        $user = auth()->user();

        $liked = $comment->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($liked) {
            $liked->delete();
            $comment->decrement('likes_count');
        } else {
            $comment->likes()->create([
                'user_id' => $user->id
            ]);
            $comment->increment('likes_count');
        }

        return back();
    }
}
