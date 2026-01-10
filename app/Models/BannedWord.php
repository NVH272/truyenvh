<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedWord extends Model
{
    protected $fillable = ['word', 'is_active', 'note'];

    // Chuẩn hoá khi lưu
    public function setWordAttribute($value)
    {
        $this->attributes['word'] = mb_strtolower(trim($value));
    }
}
