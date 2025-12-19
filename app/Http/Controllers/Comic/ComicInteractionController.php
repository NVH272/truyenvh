<?php

namespace App\Http\Controllers\Comic;

use App\Models\Comic;
use App\Models\ComicRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ComicInteractionController extends Controller
{
    // Toggle theo dõi
    public function toggleFollow(Comic $comic)
    {
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $already = $comic->followers()->where('user_id', $user->id)->exists();

        if ($already) {
            $comic->followers()->detach($user->id);
            // cập nhật tổng follows
            if ($comic->follows > 0) {
                $comic->follows -= 1;
            }
        } else {
            $comic->followers()->attach($user->id);
            $comic->follows += 1;
        }

        $comic->save();

        return back()->with('success', $already ? 'Đã bỏ theo dõi truyện.' : 'Đã theo dõi truyện.');
    }

    // Đánh giá 1–5 sao (có thể đổi bất cứ lúc nào)
    public function rate(Request $request, Comic $comic)
    {
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        // Lưu hoặc cập nhật rating của user cho comic này
        ComicRating::updateOrCreate(
            [
                'user_id'  => $user->id,
                'comic_id' => $comic->id,
            ],
            [
                'rating'   => $data['rating'],
            ]
        );

        // Re-calc avg rating + count
        $stats = $comic->ratings()
            ->selectRaw('COUNT(*) as count, AVG(rating) as avg')
            ->first();

        $comic->rating_count = $stats->count ?? 0;
        $comic->rating       = $stats->avg ?? 0;   // bạn có thể round nếu thích
        $comic->save();

        return back()->with('success', 'Đánh giá của bạn đã được cập nhật.');
    }
}
