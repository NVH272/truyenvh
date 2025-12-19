<?php

namespace App\Http\Controllers\Comic;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Comic;

class ComicFollowController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy các truyện user đã theo dõi (chỉ truyện đã được duyệt)
        $comics = $user->followedComics()
            ->where('approval_status', 'approved')
            ->with('categories')
            ->orderByPivot('created_at', 'desc') // nếu dùng withTimestamps trên pivot
            ->paginate(24);

        return view('user.comics.followed', compact('comics'));
    }
}
