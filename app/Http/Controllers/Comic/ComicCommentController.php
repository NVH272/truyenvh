<?php

namespace App\Http\Controllers\Comic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Services\ProfanityFilter;
use Illuminate\Validation\ValidationException;

class ComicCommentController extends Controller
{
    public function store(Request $request, Comic $comic)
    {
        $content = $request->input('content');

        // Xóa space / tab ở đầu mỗi dòng
        $content = preg_replace('/^[ \t]+/m', '', (string) $content);
        // Trim đầu cuối
        $content = trim($content);

        // Ghi đè content đã làm sạch vào request để validate + lưu
        $request->merge(['content' => $content]);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $hits = ProfanityFilter::findHits($content);

        if (!empty($hits)) {
            throw ValidationException::withMessages([
                'content' => 'Bình luận chứa từ ngữ bị cấm: ' . implode(', ', $hits),
            ]);
        }

        $comment = Comment::create([
            'comic_id'  => $comic->id,
            'user_id'   => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content'   => $validated['content'],
        ]);

        if ($request->parent_id) {
            Comment::where('id', $request->parent_id)->increment('replies_count');
        }

        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'comment' => [
                    'id'           => $comment->id,
                    'comic_id'     => $comment->comic_id,
                    'parent_id'    => $comment->parent_id,
                    'content'      => $comment->content,
                    'created_human' => $comment->created_at->diffForHumans(),
                    'timestamp'    => $comment->created_at->timestamp,
                    'likes_count'  => (int) ($comment->likes_count ?? 0),
                    'user'         => [
                        'id'         => $comment->user->id,
                        'name'       => $comment->user->name,
                        'avatar_url' => $comment->user->avatar_url
                            ?? 'https://ui-avatars.com/api/?name='
                            . urlencode($comment->user->name)
                            . '&background=random',
                    ],
                ],
            ]);
        }

        return back();
    }

    public function toggleReaction(Request $request, Comment $comment, string $type)
    {
        if (!in_array($type, ['like', 'dislike'])) {
            abort(404);
        }

        $user = auth()->user();

        $existing = $comment->reactions()
            ->where('user_id', $user->id)
            ->first();

        $userReaction = null;

        // TH1: Chưa có reaction -> tạo mới
        if (!$existing) {
            $comment->reactions()->create([
                'user_id' => $user->id,
                'type'    => $type,
            ]);

            if ($type === 'like') $comment->increment('likes_count');
            else $comment->increment('dislikes_count');

            $userReaction = $type;
        } elseif ($existing->type === $type) {
            // TH2: Bấm lại đúng loại -> bỏ reaction
            $existing->delete();

            if ($type === 'like') $comment->decrement('likes_count');
            else $comment->decrement('dislikes_count');

            $userReaction = null;
        } else {
            // TH3: Đang like mà bấm dislike (hoặc ngược lại) -> switch
            $oldType = $existing->type;
            $existing->update(['type' => $type]);

            if ($oldType === 'like') $comment->decrement('likes_count');
            else $comment->decrement('dislikes_count');

            if ($type === 'like') $comment->increment('likes_count');
            else $comment->increment('dislikes_count');

            $userReaction = $type;
        }

        $comment->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'likes_count'    => $comment->likes_count,
                'dislikes_count' => $comment->dislikes_count,
                'user_reaction'  => $userReaction,
                'comment_id'     => $comment->id,
            ]);
        }

        return back();
    }

    // Delete comment
    public function destroy(Request $request, Comment $comment)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        // Lấy truyện của comment để check "chủ truyện"
        // comment phải có relation comic()
        $comic = $comment->comic()->first(); // hoặc $comment->load('comic')->comic

        if (!$comic) abort(404);

        // ====== QUYỀN XOÁ ======
        $comicOwnerId = $comic->created_by;

        $isAdmin = ($user->role ?? null) === 'admin' || ($user->is_admin ?? false);
        $isComicOwner = $comicOwnerId && ((int)$comicOwnerId === (int)$user->id);

        if (!$isAdmin && !$isComicOwner) {
            abort(403);
        }

        // ====== XOÁ THEO LOẠI ======
        // Nếu là comment cha (parent_id = null) => xoá toàn bộ replies
        if ($comment->parent_id === null) {
            // Xoá replies trước để tránh mồ côi / ràng buộc FK
            Comment::where('parent_id', $comment->id)->delete();
        }

        // Xoá chính comment/reply
        $comment->delete();

        return $request->ajax()
            ? response()->json(['ok' => true, 'deleted_id' => $comment->id])
            : back()->with('success', 'Đã xoá bình luận.');
    }
}
