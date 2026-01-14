<?php

namespace App\Http\Controllers\Comic;

use App\Models\Comic;
use App\Http\Controllers\Controller;

class ComicAuthorController extends Controller
{
    public function show(string $author)
    {
        $comics = Comic::where('author', $author)
            ->orderByDesc('updated_at')
            ->paginate(20);

        abort_if($comics->isEmpty(), 404);

        return view('user.comics.author.show', compact('author', 'comics'));
    }
}
