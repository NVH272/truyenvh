<?php

namespace App\Http\Controllers\Comic;

use App\Models\Comic;
use App\Models\Author;
use App\Http\Controllers\Controller;

class ComicAuthorController extends Controller
{
    public function show(string $author)
    {
        // Tìm tác giả theo tên
        $authorModel = Author::where('name', $author)->first();

        if (!$authorModel) {
            abort(404);
        }

        // Lấy truyện của tác giả này (chỉ truyện đã duyệt)
        $comics = $authorModel->comics()
            ->where('approval_status', 'approved')
            ->with('authors')
            ->orderByDesc('updated_at')
            ->paginate(20);

        abort_if($comics->isEmpty(), 404);

        return view('user.comics.author.show', compact('author', 'comics'));
    }
}
