<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_id',
        'chapter_number',
        'title',
        'images_path',
        'page_count',
        'views',
    ];

    protected $casts = [
        'chapter_number' => 'integer',
        'page_count' => 'integer',
        'views' => 'integer',
    ];

    // Quan hệ với Comic
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    // Lấy URL đầy đủ đến thư mục chứa ảnh
    public function getImagesUrlAttribute()
    {
        return asset('storage/' . $this->images_path);
    }

    // Lấy danh sách các file ảnh trong thư mục (sắp xếp theo số thứ tự)
    public function getImageFiles()
    {
        $path = storage_path('app/public/' . $this->images_path);
        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $filePath = $path . '/' . $item;
            if (is_file($filePath)) {
                $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $files[] = $item;
                }
            }
        }

        // Sắp xếp theo số thứ tự trong tên file
        natsort($files);
        
        return array_values($files);
    }
}
