<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    public $timestamps = false; // bảng comment_likes chỉ có created_at (nếu bạn làm vậy)
    protected $fillable = ['comment_id', 'user_id', 'created_at'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
