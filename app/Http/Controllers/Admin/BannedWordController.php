<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannedWord;

class BannedWordController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $words = BannedWord::query()
            ->when($q !== '', fn($qq) => $qq->where('word', 'like', "%{$q}%"))
            ->orderByDesc('is_active')
            ->orderBy('word')
            ->paginate(30)
            ->withQueryString();

        return view('admin.banned_words.index', compact('words', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $word = mb_strtolower(trim($data['word']));
        BannedWord::updateOrCreate(
            ['word' => $word],
            [
                'note' => $data['note'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]
        );

        return back()->with('success', 'Đã thêm/cập nhật từ cấm.');
    }

    public function update(Request $request, BannedWord $bannedWord)
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bannedWord->update([
            'word' => mb_strtolower(trim($data['word'])),
            'note' => $data['note'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return back()->with('success', 'Đã cập nhật.');
    }

    public function destroy(BannedWord $bannedWord)
    {
        $bannedWord->delete();
        return back()->with('success', 'Đã xoá.');
    }
}
