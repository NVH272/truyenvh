@extends('layouts.app')

@section('title', 'Danh sách truyện của bạn')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-slate-100">Danh sách truyện của bạn</h1>

        <form class="flex gap-2" method="GET">
            <input name="q" value="{{ request('q') }}" placeholder="Tìm theo tên truyện..."
                class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-slate-200 text-sm">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">Tìm</button>
        </form>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/70 text-slate-300">
                <tr>
                    <th class="text-left px-4 py-3">Truyện</th>
                    <th class="text-left px-4 py-3">Trạng thái</th>
                    <th class="text-center px-4 py-3">Số chương</th>
                    <th class="text-left px-4 py-3">Cập nhật</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-700">
                @forelse ($comics as $comic)
                <tr
                    class="hover:bg-slate-700/40 cursor-pointer transition"
                    onclick="window.location='{{ route('poster.chapters', $comic->slug) }}'">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-slate-100">{{ $comic->title }}</div>
                        <div class="text-xs text-slate-400">{{ $comic->slug }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-200">
                        {{ $comic->status }}
                    </td>
                    <td class="px-4 py-3 text-center text-slate-200">
                        {{ $comic->chapters_count }}
                    </td>
                    <td class="px-4 py-3 text-slate-300">
                        {{ optional($comic->updated_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                        Bạn chưa đăng truyện nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $comics->links() }}
    </div>
</div>
@endsection