<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Comic extends Model
{
    use HasFactory;

    // Các cột cho phép gán hàng loạt (theo migration comics bạn đã thiết kế)
    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'author',
        'status',
        'views',
        'follows',
        'rating',
        'rating_count',
        'chapter_count',
        'published_at',
        'last_chapter_at',
        'created_by',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'last_chapter_at' => 'datetime',
        'approved_at'     => 'datetime',
        'chapter_count' => 'integer',
    ];

    // Lấy URL ảnh bìa
    public function getCoverUrlAttribute()
    {
        // Nếu có ảnh upload, dùng ảnh đó từ storage/public/uploads/comics
        if ($this->cover_image) {
            return asset('storage/uploads/comics/' . $this->cover_image);
        }

        // Nếu không có ảnh, dùng ảnh mặc định từ storage/public/images
        return asset('storage/images/default-cover.png');
    }

    // Quan hệ N-N với Category    
    public function categories()
    {
        // pivot: category_comic (category_id, comic_id)
        return $this->belongsToMany(Category::class, 'category_comic', 'comic_id', 'category_id');
    }

    // Quan hệ với User (người tạo truyện)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Quan hệ với User (người duyệt truyện)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Quan hệ 1-N với Chapter
    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'comic_id');
    }

    // Quan hệ 1-N với ComicRating
    public function ratings()
    {
        return $this->hasMany(\App\Models\ComicRating::class);
    }

    // Quan hệ N-N với User qua bảng comic_follows
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comic_follows')
            ->withTimestamps();
    }
}
