@extends('layouts.app')

@section('title', 'Danh sách chương')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('poster.index') }}" class="text-sm text-slate-300 hover:text-white">
                ← Quay lại danh sách truyện
            </a>
            <h1 class="text-lg font-bold text-slate-100 mt-1">
                Chương của truyện: <span class="text-blue-400">{{ $comic->title }}</span>
            </h1>
        </div>

        <a href="{{ route('user.comics.chapters.create', ['comic' => $comic->id, 'redirect_to' => url()->full()]) }}"
            class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold">
            + Thêm chapter
        </a>

    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/70 text-slate-300">
                <tr>
                    <th class="text-left px-4 py-3">Chapter</th>
                    <th class="text-left px-4 py-3">Tiêu đề</th>
                    <th class="text-center px-4 py-3">Lượt xem</th>
                    <th class="text-left px-4 py-3">Ngày đăng</th>
                    <th class="text-right px-4 py-3">Hành động</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-700">
                @forelse ($chapters as $chapter)
                <tr class="hover:bg-slate-700/40 transition">
                    <td class="px-4 py-3 font-semibold text-slate-100">
                        Ch. {{ $chapter->chapter_number }}
                    </td>
                    <td class="px-4 py-3 text-slate-200">
                        {{ $chapter->title ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center text-slate-200">
                        {{ $chapter->views ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-slate-300">
                        {{ optional($chapter->created_at)->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{-- SỬA --}}
                        <a href="{{ route('user.comics.chapters.edit', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                            class="inline-flex items-center justify-center w-8 h-8
                  rounded-lg border border-gray-300
                  text-gray-700 hover:text-blue-600
                  hover:border-blue-500 hover:bg-blue-50
                  transition-all"
                            title="Sửa chapter">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>

                        {{-- XÓA (nếu có) --}}
                        <form action="{{ route('user.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                            method="POST" class="inline"
                            onsubmit="return confirm('Xóa chapter này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center justify-center w-8 h-8
                       rounded-lg border border-gray-300
                       text-gray-700 hover:text-red-600
                       hover:border-red-500 hover:bg-red-50
                       transition-all"
                                title="Xóa chapter">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                        Truyện này chưa có chapter nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $chapters->links() }}
    </div>
</div>
@endsection