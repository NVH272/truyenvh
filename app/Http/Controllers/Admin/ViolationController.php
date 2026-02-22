<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use App\Models\BannedWord;

class ViolationController extends Controller
{
    public function index()
    {
        $reports = CommentReport::query()
            ->with([
                'reporter:id,name',
                'comic:id,title,slug',
                'comment:id,comic_id,user_id,parent_id,content,created_at',
                'comment.user:id,name',
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $bannedWordsCount = BannedWord::count();
        $pendingCount = CommentReport::where('status', 'pending')->count();
        $resolvedCount = CommentReport::where('status', 'resolved')->count();

        return view('admin.violations.index', compact('reports', 'bannedWordsCount', 'pendingCount', 'resolvedCount'));
    }

    public function banUserAndDeleteComment($id)
    {
        // Tìm báo cáo
        $report = \App\Models\CommentReport::with(['comment', 'comment.user'])->findOrFail($id);

        // Sử dụng DB transaction để đảm bảo cả 2 hành động đều thành công hoặc đều thất bại
        \Illuminate\Support\Facades\DB::transaction(function () use ($report) {
            $comment = $report->comment;

            if ($comment) {
                $user = $comment->user;

                // Khoá tài khoản người dùng
                if ($user) {
                    $user->is_active = 0;
                    $user->save();
                }

                $comment->delete();
            }

            // Cập nhật trạng thái báo cáo thành đã xử lý
            $report->update(['status' => 'resolved']);

            if ($comment) {
                \App\Models\CommentReport::where('comment_id', $comment->id)
                    ->update(['status' => 'resolved']);
            }
        });

        return back()->with('success', 'Đã xoá bình luận và khoá tài khoản thành công.');
    }
}
