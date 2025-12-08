<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // THÊM ĐỦ CÁC CỘT MỚI VÀO ĐÂY
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    // Quan hệ với Comic nếu có
    public function comics()
    {
        return $this->belongsToMany(Comic::class, 'category_comic', 'category_id', 'comic_id');
    }
}
