<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'last_chapter_at' => 'datetime',
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

    // Quan hệ 1-N với Chapter
    public function chapters()
    {
        // return $this->hasMany(Chapter::class, 'comic_id');
    }

    /**
     * (Gợi ý) Quan hệ N-N với User thông qua bảng follow / favorite
     * Chỉ dùng khi bạn tạo bảng này, có thể sửa tên bảng tùy ý
     */
    // public function followers()
    // {
    //     return $this->belongsToMany(User::class, 'comic_user_follows', 'comic_id', 'user_id')
    //                 ->withTimestamps();
    // }

    /**
 * (Gợi ý) Quan hệ 1-N với Rating/Bình luận nếu bạn tách ra bảng riêng
 */
    // public function ratings()
    // {
    //     return $this->hasMany(Rating::class, 'comic_id');
    // }
}
