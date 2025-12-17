<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    // Bảng này không có created_at / updated_at nên tắt timestamps
    public $timestamps = false;
    protected $table = 'comment_likes';

    protected $fillable = ['comment_id', 'user_id', 'type'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
