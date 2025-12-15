<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicRating extends Model
{
    protected $fillable = ['user_id', 'comic_id', 'rating'];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
