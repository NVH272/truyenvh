<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    protected $fillable = [
        'comment_id',
        'comic_id',
        'reported_by',
        'status'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
