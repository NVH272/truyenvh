@extends('layouts.admin')
@section('title', 'Từ cấm')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Danh sách từ cấm</h1>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Tìm từ..."
                class="border rounded-lg px-3 py-2 text-sm">
            <button class="px-3 py-2 bg-slate-800 text-white rounded-lg text-sm">Tìm</button>
        </form>
    </div>

    {{-- Thêm mới --}}
    <div class="bg-white border rounded-xl p-4">
        <form method="POST" action="{{ route('admin.banned_words.store') }}" class="flex flex-wrap gap-2 items-end">
            @csrf
            <div>
                <label class="text-xs text-gray-500">Từ</label>
                <input name="word" required class="border rounded-lg px-3 py-2 text-sm w-56" placeholder="vd: abc">
            </div>
            <div class="flex-1 min-w-[240px]">
                <label class="text-xs text-gray-500">Ghi chú</label>
                <input name="note" class="border rounded-lg px-3 py-2 text-sm w-full" placeholder="tuỳ chọn">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked>
                Kích hoạt
            </label>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Thêm</button>
        </form>
    </div>

    {{-- Danh sách --}}
    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="divide-y">
            @forelse($words as $w)
            <div class="p-4 flex items-start justify-between gap-4">
                <form method="POST" action="{{ route('admin.banned_words.update', $w->id) }}" class="flex-1 flex flex-wrap gap-2 items-end">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-xs text-gray-500">Từ</label>
                        <input name="word" value="{{ $w->word }}" class="border rounded-lg px-3 py-2 text-sm w-56">
                    </div>
                    <div class="flex-1 min-w-[240px]">
                        <label class="text-xs text-gray-500">Ghi chú</label>
                        <input name="note" value="{{ $w->note }}" class="border rounded-lg px-3 py-2 text-sm w-full">
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ $w->is_active ? 'checked' : '' }}>
                        Kích hoạt
                    </label>
                    <button class="px-3 py-2 bg-slate-800 text-white rounded-lg text-sm">Lưu</button>
                </form>

                <form method="POST" action="{{ route('admin.banned_words.destroy', $w->id) }}"
                    onsubmit="return confirm('Xoá từ này?');">
                    @csrf @method('DELETE')
                    <button class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm">Xoá</button>
                </form>
            </div>
            @empty
            <div class="p-6 text-gray-500">Chưa có từ cấm.</div>
            @endforelse
        </div>
    </div>

    {{ $words->links() }}
</div>
@endsection