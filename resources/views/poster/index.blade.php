@extends('layouts.app')

@section('title', 'Danh sách truyện của bạn')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    {{-- HEADER: TITLE & TOOLBAR --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Quản lý Truyện</h1>
            <p class="text-slate-500 text-sm mt-1">Quản lý danh sách và trạng thái truyện tranh của bạn.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search Form --}}
            <form class="relative group" method="GET">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                </div>
                <input name="q" value="{{ request('q') }}" placeholder="Tìm tên truyện..."
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm w-full sm:w-64
                        focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all shadow-sm placeholder-slate-400">
            </form>

            {{-- Create Button --}}
            <a href="{{ route('user.my-comics.create') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-md shadow-blue-200 transition-all hover:-translate-y-0.5 gap-2">
                <i class="fas fa-plus"></i>
                <span>Thêm truyện mới</span>
            </a>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden flex flex-col">

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                {{-- Table Head --}}
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs font-bold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Thông tin truyện</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center">Tiến độ</th>
                        <th class="px-6 py-4 text-center">Lượt xem</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="divide-y divide-slate-100">
                    @forelse ($comics as $comic)
                    <tr class="group hover:bg-blue-50/40 transition-colors duration-200 cursor-pointer"
                        onclick="window.location='{{ route('poster.chapters', $comic->slug) }}'">

                        {{-- 1. THÔNG TIN TRUYỆN --}}
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-4">
                                {{-- Ảnh Bìa --}}
                                <div class="relative flex-shrink-0 w-14 h-20 rounded-lg overflow-hidden shadow-sm border border-slate-200
                                                group-hover:shadow-md group-hover:border-blue-300 transition-all duration-300">
                                    <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                </div>

                                {{-- Text --}}
                                <div class="flex flex-col justify-center py-1">
                                    <h3 class="text-base font-bold text-slate-800 group-hover:text-blue-700 transition-colors line-clamp-1 mb-1" title="{{ $comic->title }}">
                                        {{ $comic->title }}
                                    </h3>

                                    {{-- TÁC GIẢ --}}
                                    <div class="text-xs text-slate-500 flex items-center gap-1.5 mb-1.5">
                                        <i class="fas fa-pen-nib text-slate-400 text-[10px]"></i>
                                        <span class="font-medium text-slate-600 truncate max-w-[200px]">
                                            {{ $comic->author ?? 'Đang cập nhật' }}
                                        </span>
                                    </div>

                                    {{-- Rating --}}
                                    <div class="flex items-center gap-1 text-slate-400 text-xs">
                                        <x-rating-stars :rating="$comic->rating_avg ?? 0" sizeClass="text-[10px]" />
                                        <span class="font-semibold text-slate-600">{{ number_format($comic->rating_avg ?? 0, 1) }}</span>
                                        <span class="text-slate-400">|</span>
                                        <i class="fas fa-heart text-xs text-rose-400"></i>
                                        <span class="font-mono text-xs">{{ number_format($comic->follows ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- 2. TRẠNG THÁI --}}
                        <td class="px-6 py-4 text-center align-middle">
                            @php
                            $statusStyle = match($comic->status) {
                            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'ongoing' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'dropped' => 'bg-rose-100 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                            };
                            $statusLabel = match($comic->status) {
                            'completed' => 'Đã hoàn thành',
                            'ongoing' => 'Đang tiến hành',
                            'dropped' => 'Tạm dừng',
                            default => $comic->status
                            };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusStyle }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- 3. TIẾN ĐỘ --}}
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="inline-flex flex-col items-center justify-center bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 min-w-[60px]">
                                <span class="text-lg font-bold text-slate-700 leading-none">{{ $comic->chapters_count ?? 0 }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wide">Chương</span>
                            </div>
                        </td>

                        {{-- 4. LƯỢT XEM --}}
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="inline-flex flex-col items-center justify-center">
                                <span class="text-lg font-bold text-slate-700 leading-none">{{ number_format($comic->views ?? 0) }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wide">Lượt xem</span>
                            </div>
                        </td>

                        {{-- 5. THAO TÁC --}}
                        <td class="px-6 py-4 text-right align-middle" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">

                                {{-- View --}}
                                <a href="{{ route('user.comics.show', $comic->slug) }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-teal-600 hover:bg-teal-50 border border-slate-200 hover:border-teal-200 transition-all shadow-sm"
                                    title="Xem trang truyện" target="_blank">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('user.my-comics.edit', $comic) }}?redirect_to={{ urlencode(url()->full()) }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-all shadow-sm"
                                    title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('user.my-comics.destroy', $comic) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa truyện này? Hành động này không thể hoàn tác!')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                    <button type="submit"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 border border-slate-200 hover:border-red-200 transition-all shadow-sm"
                                        title="Xóa truyện">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                    <i class="fas fa-layer-group text-4xl text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg">Chưa có truyện nào</h3>
                                <p class="text-slate-500 text-sm mt-1 mb-6 max-w-xs mx-auto">Danh sách truyện của bạn đang trống. Hãy bắt đầu chia sẻ đam mê ngay!</p>
                                <a href="{{ route('user.my-comics.create') }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-plus"></i> Tạo truyện mới
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION - ĐÃ DI CHUYỂN VÀO TRONG CARD --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50
                    flex flex-col sm:flex-row justify-between items-center gap-4">

            {{-- Thông tin hiển thị (Đã đổi màu cho hợp theme sáng) --}}
            <div class="text-xs text-slate-500 font-medium">
                Hiển thị
                <span class="text-slate-700 font-bold">{{ $comics->firstItem() ?? 0 }}</span>
                -
                <span class="text-slate-700 font-bold">{{ $comics->lastItem() ?? 0 }}</span>
                của
                <span class="text-slate-700 font-bold">{{ $comics->total() }}</span>
                truyện
            </div>

            {{-- Controls --}}
            <div class="flex items-center gap-1">

                {{-- Prev --}}
                @if ($comics->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400
                                cursor-not-allowed border border-slate-200">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
                @else
                <a href="{{ $comics->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-lg bg-white text-slate-600
                            hover:bg-slate-50 hover:text-blue-600 border border-slate-300 shadow-sm transition-all">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($comics->getUrlRange(max(1, $comics->currentPage() - 1), min($comics->lastPage(), $comics->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3.5 py-1.5 text-sm font-bold rounded-lg border transition-all shadow-sm
                    {{ $page == $comics->currentPage()
                        ? 'bg-blue-600 border-blue-600 text-white shadow-blue-200' 
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:border-blue-200' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- Next --}}
                @if ($comics->hasMorePages())
                <a href="{{ $comics->nextPageUrl() }}"
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