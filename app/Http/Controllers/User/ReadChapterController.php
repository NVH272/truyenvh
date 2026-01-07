<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comic;
use App\Models\Chapter;

class ReadChapterController extends Controller
{
    public function show($comic, $chapter_number)
    {
        // comic id
        $comic = Comic::findOrFail($comic);

        // tìm chapter theo comic_id + chapter_number
        $chapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', (int)$chapter_number)
            ->firstOrFail();

        // load pages
        $chapter->load(['pages']);

        return view('user.comics.chapters.read', compact('comic', 'chapter'));
    }
}
