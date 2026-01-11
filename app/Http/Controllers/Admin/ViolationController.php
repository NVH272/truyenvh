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

        return view('admin.violations.index', compact('reports', 'bannedWordsCount'));
    }
}
