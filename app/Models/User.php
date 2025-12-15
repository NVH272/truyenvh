<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Comic;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'avatar', // Đảm bảo trường này có trong fillable
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Mutator: Tự động normalize email khi lưu vào database
     * - Chuyển về lowercase
     * - Trim khoảng trắng
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    // Helper: Tự động lấy ảnh storage hoặc ảnh tạo theo tên
    public function getAvatarUrlAttribute()
    {
        // Nếu có ảnh upload trong database
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Mặc định: Tạo avatar theo tên từ ui-avatars.com
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=random&color=fff&size=128";
    }

    public function followedComics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class, 'comic_follows')
            ->withTimestamps();
    }
}
