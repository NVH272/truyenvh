@extends('layouts.admin')
@section('title', 'Quản lý Từ cấm')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- HEADER & SEARCH --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-100 tracking-tight">Danh sách từ cấm</h1>
            <p class="text-slate-400 text-sm mt-1">Quản lý các từ ngữ không được phép sử dụng trên hệ thống.</p>
        </div>
        <form method="GET" class="relative group w-full md:w-auto">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-500 group-focus-within:text-blue-500 transition-colors"></i>
            </div>
            <input name="q" value="{{ $q }}" placeholder="Tìm kiếm từ khóa..."
                class="pl-10 pr-20 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 text-sm w-full md:w-80
                focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-lg placeholder-slate-500">
            <button class="absolute right-1 top-1 bottom-1 px-4 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg text-xs font-bold transition-colors border border-slate-600">
                Tìm
            </button>
        </form>
    </div>

    {{-- THÊM MỚI (ADD CARD) --}}
    <div class="bg-slate-800 border border-slate-700/60 rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-slate-700/30 px-6 py-4 border-b border-slate-700/60 flex items-center gap-2">
            <i class="fas fa-plus-circle text-blue-500"></i>
            <h3 class="font-semibold text-slate-200 text-sm uppercase tracking-wide">Thêm từ mới - Cập nhật</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.banned_words.store') }}" class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                @csrf
                <div class="w-full md:w-1/3">
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase">Từ khóa <span class="text-red-500">*</span></label>
                    <input name="word" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-600 bg-slate-900/50 text-slate-200 
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm font-medium placeholder-slate-600"
                        placeholder="Ví dụ: badword">
                </div>

                <div class="w-full md:flex-1">
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase">Ghi chú</label>
                    <input name="note"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-600 bg-slate-900/50 text-slate-200 
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm placeholder-slate-600"
                        placeholder="Lý do cấm hoặc ghi chú thêm...">
                </div>

                <div class="flex items-center gap-4 h-[42px]">
                    <label class="flex items-center gap-2 text-sm text-slate-300 font-medium cursor-pointer select-none group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="peer sr-only">
                            <div class="w-9 h-5 bg-slate-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                        <span class="group-hover:text-white transition-colors">Kích hoạt</span>
                    </label>
                    <button class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5 border border-blue-500">
                        <i class="fas fa-save mr-1"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH (LIST CARD) --}}
    <div class="bg-slate-800 border border-slate-700/60 rounded-2xl shadow-xl overflow-hidden">
        {{-- Table Header --}}
        <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 bg-slate-900/50 border-b border-slate-700/60 text-xs font-bold text-slate-400 uppercase">
            <div class="col-span-3">Từ khóa</div>
            <div class="col-span-5">Ghi chú</div>
            <div class="col-span-2 text-center">Trạng thái</div>
            <div class="col-span-2 text-right">Hành động</div>
        </div>

        <div class="divide-y divide-slate-700/60">
            @forelse($words as $w)
            <div class="group p-4 md:px-6 md:py-3 hover:bg-slate-700/30 transition-colors">
                <div class="flex flex-col md:grid md:grid-cols-12 gap-4 items-center">

                    {{-- FORM EDIT WRAPPER --}}
                    <form method="POST" action="{{ route('admin.banned_words.update', $w->id) }}" id="form-update-{{ $w->id }}" class="contents">
                        @csrf @method('PUT')

                        {{-- Cột Từ khóa --}}
                        <div class="w-full md:col-span-3">
                            <label class="md:hidden text-xs text-slate-500 mb-1">Từ khóa</label>
                            <input name="word" value="{{ $w->word }}"
                                class="w-full px-3 py-2 rounded-lg border border-transparent bg-transparent 
                                hover:bg-slate-700/50 hover:border-slate-600 
                                focus:bg-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 
                                outline-none text-sm font-bold text-slate-200 transition-all placeholder-slate-600">
                        </div>

                        {{-- Cột Ghi chú --}}
                        <div class="w-full md:col-span-5">
                            <label class="md:hidden text-xs text-slate-500 mb-1">Ghi chú</label>
                            <input name="note" value="{{ $w->note }}"
                                class="w-full px-3 py-2 rounded-lg border border-transparent bg-transparent 
                                hover:bg-slate-700/50 hover:border-slate-600 
                                focus:bg-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 
                                outline-none text-sm text-slate-400 focus:text-slate-200 transition-all placeholder-slate-600">
                        </div>

                        {{-- Cột Trạng thái (Toggle Switch Style) --}}
                        <div class="w-full md:col-span-2 flex md:justify-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $w->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="relative w-9 h-5 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ms-3 text-xs font-medium {{ $w->is_active ? 'text-emerald-400' : 'text-slate-500' }}">
                                    {{ $w->is_active ? 'Bật' : 'Tắt' }}
                                </span>
                            </label>
                        </div>

                        <div class="hidden"></div>
                    </form>

                    {{-- Cột Hành động --}}
                    <div class="w-full md:col-span-2 flex items-center justify-end gap-2 opacity-100 md:opacity-60 md:group-hover:opacity-100 transition-opacity">
                        {{-- Nút Lưu --}}
                        <button type="submit" form="form-update-{{ $w->id }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 border border-transparent hover:border-blue-500/30 transition-all"
                            title="Lưu thay đổi">
                            <i class="fas fa-check"></i>
                        </button>

                        {{-- Nút Xóa --}}
                        <form method="POST" action="{{ route('admin.banned_words.destroy', $w->id) }}"
                            class="inline-block"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xoá từ \'{{ $w->word }}\'?');">
                            @csrf @method('DELETE')
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/30 transition-all"
                                title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
            @empty
            <div class="p-16 text-center flex flex-col items-center justify-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-700/50 mb-4 animate-pulse">
                    <i class="fas fa-shield-alt text-4xl text-slate-500"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-300">Danh sách trống</h3>
                <p class="text-slate-500 text-sm mt-2 max-w-sm">Chưa có từ cấm nào được thêm vào hệ thống. Hãy thêm từ mới để bắt đầu quản lý.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination (Dark Theme) --}}
    @if($words->hasPages())
    <div class="flex justify-center pt-4">
        {{ $words->links() }}
        {{-- Lưu ý: Bạn cần đảm bảo view pagination của Laravel đã được publish và chỉnh sang dark mode, 
             hoặc bọc nó trong một div có class dark:text-white để hiển thị tốt --}}
    </div>
    @endif
</div>
@endsection