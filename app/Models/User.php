<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // dùng cho Auth
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nếu bảng của bạn tên là "users" thì không cần khai báo gì thêm.
    // Nếu bạn dùng tên khác, ví dụ "tbl_users", thì thêm:
    // protected $table = 'tbl_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // nếu bạn có cột role
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
