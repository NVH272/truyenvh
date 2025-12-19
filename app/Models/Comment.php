<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommentLike;


class Comment extends Model
{
    protected $fillable = [
        'comic_id',
        'user_id',
        'parent_id',
        'content',
        'likes_count',
        'dislikes_count',
        'replies_count',
    ];

    // User comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comic
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    // Reply
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user')
            ->oldest();
    }

    // Parent
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Likes (only type = like)
    public function likes()
    {
        return $this->hasMany(CommentLike::class, 'comment_id')
            ->where('type', 'like');
    }

    public function isLikedBy($user)
    {
        if (!$user) return false;

        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->exists();
    }

    // reactions
    public function reactions()
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }

    public function dislikes()
    {
        return $this->reactions()->where('type', 'dislike');
    }

    public function isDislikedBy($user): bool
    {
        return $user
            ? $this->reactions()->where('user_id', $user->id)->where('type', 'dislike')->exists()
            : false;
    }
}
