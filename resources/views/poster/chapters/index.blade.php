@extends('layouts.app')

@section('title', 'Danh sách chương - ' . $comic->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    {{-- HEADER: INFO & ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="flex items-start gap-5">
            {{-- Comic Cover Thumbnail --}}
            <div class="shrink-0 w-20 h-28 rounded-lg overflow-hidden shadow-md border border-slate-200 hidden sm:block group relative">
                <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
            </div>

            <div class="space-y-2">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                    <a href="{{ route('poster.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                        <i class="fas fa-arrow-left text-xs"></i> Truyện của tôi
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-700">Quản lý chương</span>
                </div>

                {{-- Title --}}
                <h1 class="text-3xl font-black text-slate-800 tracking-tight leading-tight line-clamp-1" title="{{ $comic->title }}">
                    {{ $comic->title }}
                </h1>

                {{-- Stats Summary --}}
                <div class="flex flex-wrap items-center gap-3 text-xs font-bold text-slate-600">
                    <span class="flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-sm">
                        <i class="fas fa-layer-group text-blue-500"></i> {{ $chapters->total() }} chương
                    </span>
                    <span class="flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-sm">
                        <i class="fas fa-eye text-green-500"></i> {{ number_format($chapters->sum('views')) }} lượt xem
                    </span>
                </div>
            </div>
        </div>

        {{-- ADD BUTTON --}}
        <a href="{{ route('user.comics.chapters.create', ['comic' => $comic->id, 'redirect_to' => url()->full()]) }}"
            class="group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm font-bold shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 active:scale-95 whitespace-nowrap">
            <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
            <span>Thêm chương mới</span>
        </a>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col">

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                {{-- Table Head --}}
                <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-24 text-center">#</th>
                        <th class="px-6 py-4">Tên chương</th>
                        <th class="px-6 py-4 text-center">Lượt xem</th>
                        <th class="px-6 py-4">Thời gian đăng</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                {{-- Table Body (Compact Mode) --}}
                <tbody class="divide-y divide-slate-100">
                    @forelse ($chapters as $chapter)
                    {{-- Row Hover --}}
                    <tr class="group hover:bg-blue-50/40 transition-colors duration-200">

                        {{-- 1. CHAPTER NUMBER --}}
                        <td class="px-6 py-2.5 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-7 rounded bg-slate-100 text-slate-700 font-bold font-mono text-xs border border-slate-200 group-hover:bg-white group-hover:border-blue-200 group-hover:text-blue-600 transition-all">
                                {{ $chapter->chapter_number }}
                            </span>
                        </td>

                        {{-- 2. TIÊU ĐỀ --}}
                        <td class="px-6 py-2.5">
                            <div class="flex flex-col justify-center">
                                @if($chapter->title)
                                <span class="font-medium text-slate-700 text-sm group-hover:text-blue-700 transition-colors line-clamp-1">{{ $chapter->title }}</span>
                                @else
                                <span class="text-slate-400 italic text-sm">Chapter {{ $chapter->chapter_number }}</span>
                                @endif
                                {{-- ID nhỏ hơn nữa --}}
                                <span class="text-[9px] text-slate-400 font-mono uppercase tracking-wide opacity-100 group-hover:opacity-100 transition-opacity leading-none mt-0.5">ID: {{ $chapter->id }}</span>
                            </div>
                        </td>

                        {{-- 3. LƯỢT XEM --}}
                        <td class="px-6 py-2.5 text-center">
                            <div class="inline-flex items-center gap-1.5 text-slate-600 font-medium text-xs bg-white px-2.5 py-0.5 rounded-full border border-slate-200 shadow-sm group-hover:border-blue-200 transition-colors">
                                <i class="far fa-eye text-[10px] text-blue-500"></i>
                                {{ number_format($chapter->views ?? 0) }}
                            </div>
                        </td>

                        {{-- 4. NGÀY ĐĂNG --}}
                        <td class="px-6 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-blue-500 group-hover:bg-blue-50 transition-colors">
                                    <i class="far fa-calendar-alt text-[10px]"></i>
                                </div>
                                <div class="flex flex-col text-xs leading-tight">
                                    <span class="font-bold text-slate-700">{{ optional($chapter->created_at)->format('d/m/Y') }}</span>
                                    <span class="text-slate-400 text-[10px]">{{ optional($chapter->created_at)->format('H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 5. THAO TÁC --}}
                        <td class="px-6 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-90 group-hover:opacity-100 transition-opacity">

                                {{-- View --}}
                                <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id, 'chapter_number' => $chapter->chapter_number]) }}"
                                    class="w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:text-teal-600 hover:bg-teal-50 border border-transparent hover:border-teal-200 transition-all"
                                    title="Đọc thử" target="_blank">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('user.comics.chapters.edit', ['comic' => $comic->id, 'chapter' => $chapter->id, 'redirect_to' => url()->full()]) }}"
                                    class="w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                    title="Sửa chapter">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('user.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                                    method="POST" class="inline-block"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này không? Hành động này không thể hoàn tác!')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                    <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition-all"
                                        title="Xóa chapter">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            {{-- Giữ nguyên phần empty state --}}
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                    <i class="fas fa-layer-group text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg">Chưa có chapter nào</h3>
                                <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Truyện này đang trống. Hãy thêm chương đầu tiên ngay!</p>
                                <a href="{{ route('user.comics.chapters.create', ['comic' => $comic->id, 'redirect_to' => url()->full()]) }}"
                                    class="mt-5 text-green-600 hover:text-green-700 text-sm font-bold hover:underline">
                                    + Thêm chapter ngay
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION - ĐÃ ĐƯỢC THÊM VÀO ĐÂY --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50
                    flex flex-col sm:flex-row justify-between items-center gap-4">

            {{-- Thông tin hiển thị --}}
            <div class="text-xs text-slate-500 font-medium">
                Hiển thị
                <span class="text-slate-700 font-bold">{{ $chapters->firstItem() ?? 0 }}</span>
                -
                <span class="text-slate-700 font-bold">{{ $chapters->lastItem() ?? 0 }}</span>
                của
                <span class="text-slate-700 font-bold">{{ $chapters->total() }}</span>
                chương
            </div>

            {{-- Controls --}}
            <div class="flex items-center gap-1">

                {{-- Prev --}}
                @if ($chapters->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400
                                cursor-not-allowed border border-slate-200">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
                @else
                <a href="{{ $chapters->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-lg bg-white text-slate-600
                            hover:bg-slate-50 hover:text-blue-600 border border-slate-300 shadow-sm transition-all">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($chapters->getUrlRange(max(1, $chapters->currentPage() - 1), min($chapters->lastPage(), $chapters->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3.5 py-1.5 text-sm font-bold rounded-lg border transition-all shadow-sm
                    {{ $page == $chapters->currentPage()
                        ? 'bg-blue-600 border-blue-600 text-white shadow-blue-200' 
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:border-blue-200' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- Next --}}
                @if ($chapters->hasMorePages())
                <a href="{{ $chapters->nextPageUrl() }}"
                    class="px-3 py-1.5 rounded-lg bg-white text-slate-600
                            hover:bg-slate-50 hover:text-blue-600 border border-slate-300 shadow-sm transition-all">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                @else
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400
                                cursor-not-allowed border border-slate-200">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
                @endif

            </div>
        </div>

    </div> {{-- End Card --}}
</div>
@endsection