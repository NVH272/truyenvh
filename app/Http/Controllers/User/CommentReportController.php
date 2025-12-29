<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentReportController extends Controller
{
    public function store(Request $request, Comment $comment)
    {
        $user = $request->user();
        if (!$user) abort(403);

        // tránh spam: 1 user báo cáo 1 comment 1 lần
        CommentReport::firstOrCreate([
            'comment_id' => $comment->id,
            'comic_id' => $comment->comic_id,
            'reported_by' => $user->id,
        ], [
            'status' => 'pending',
        ]);

        return $request->ajax()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Đã gửi báo cáo tới quản trị viên.');
    }
}
