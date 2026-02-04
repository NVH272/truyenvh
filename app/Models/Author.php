<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Một tác giả có thể có nhiều truyện.
     */
    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class, 'author_comic', 'author_id', 'comic_id');
    }
}

